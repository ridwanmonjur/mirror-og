<?php

namespace App\Filament\Resources\WithdrawalPasswordResource\Pages;

use App\Filament\Resources\WithdrawalPasswordResource;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\WithdrawalPassword;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WithdrawalCsvExportMail;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalPasswordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    DatePicker::make('date_from')
                        ->label('Date From')
                        ->timezone('Asia/Kuala_Lumpur')
                        ->required(),
                    DatePicker::make('date_to')
                        ->label('Date To')
                        ->timezone('Asia/Kuala_Lumpur')
                        ->required()
                        ->after('date_from'),

                ])
                ->action(function (array $data) {
                    return $this->exportWithdrawals($data);
                }),
        ];
    }

    protected function exportWithdrawals(array $data)
    {
        $dateFrom = $data['date_from'];
        $dateTo = $data['date_to'];
        $includeBankDetails = $data['include_bank_details'] ?? true;
        $user = Auth::user();

        // Query withdrawals within the date range
        $withdrawals = Withdrawal::with('user')
            ->get();

        if ($withdrawals->isEmpty()) {
            Notification::make()
                ->title('No withdrawals found')
                ->body('No withdrawal records found for the selected date range.')
                ->warning()
                ->send();

            return;
        }

        $token = Str::random(10);

        DB::table('2fa_links')->insert([
            'user_id' => $user->id,
            'withdrawal_history_token' => $token,
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Store export data in session/cache for retrieval
        $exportData = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'include_bank_details' => $includeBankDetails,
            'user_id' => $user->id,
        ];

        cache()->put("withdrawal_export_{$token}", $exportData, now()->addHours(24));

        // Generate download link
        $downloadLink = url("/download-withdrawal-csv/{$token}");

        // Send email with download link
        try {
            Mail::to($user->email)->queue(new WithdrawalCsvExportMail($user->name, $downloadLink));

            Notification::make()
                ->title('Export link sent')
                ->body('A download link has been sent to your email address.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Export failed')
                ->body('Unable to send email with download link.')
                ->danger()
                ->send();
        }
    }

    protected function generateCsvContent($withdrawals, bool $includeBankDetails): string
    {
        $headers = [
            'ID',
            'User ID',
            'User Name',
            'User Email',
            'Amount (RM)',
            'Status',
            'Requested At',
        ];

        if ($includeBankDetails) {
            $headers = array_merge($headers, [
                'Bank Name',
                'Account Number',
                'Account Holder Name',
            ]);
        }

        $csvData = [];
        $csvData[] = $headers;

        $wallet = null;
        if (isset($withdrawals[0])) {
            $wallet = Wallet::retrieveOrCreateCache($withdrawals[0]->user_id);
        }

        foreach ($withdrawals as $withdrawal) {
            $row = [
                $withdrawal->id,
                $withdrawal->user_id,
                $withdrawal->user->name ?? 'N/A',
                $withdrawal->user->email ?? 'N/A',
                number_format($withdrawal->withdrawal, 2),
                ucfirst($withdrawal->status),
                $withdrawal->requested_at ? $withdrawal->requested_at->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s') : 'N/A',
            ];

            if ($includeBankDetails) {
                $row = array_merge($row, [
                    $wallet?->bank_name ?? 'N/A',
                    $wallet?->account_number ?? 'N/A',
                    $wallet?->account_holder_name ?? 'N/A',
                ]);
            }

            $csvData[] = $row;
        }

        // Convert to CSV format
        $output = fopen('php://temp', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }
}
