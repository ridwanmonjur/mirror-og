<?php

namespace App\Filament\Resources\WithdrawalPasswordResource\Pages;

use App\Filament\Resources\WithdrawalPasswordResource;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use ZipArchive;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;


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
                    TextInput::make('password')
                        ->label('CSV Password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required()
                        ->minLength(6)
                        ->helperText('Password must be at least 6 characters long'),
                    Toggle::make('include_bank_details')
                        ->label('Include Bank Account Details')
                        ->default(true)
                        ->helperText('Include user bank account information in the export'),
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
        $password = $data['password'];
        $includeBankDetails = $data['include_bank_details'] ?? true;

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

        // Generate CSV content
        $csvContent = $this->generateCsvContent($withdrawals, $includeBankDetails);
        
        // Create temporary files
        $tempDir = storage_path('app/temp/withdrawals');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $csvFileName = 'withdrawals_' . date('Y-m-d_H-i-s') . '.csv';
        $csvPath = $tempDir . '/' . $csvFileName;
        $zipPath = $tempDir . '/withdrawals_export_' . date('Y-m-d_H-i-s') . '.zip';

        // Write CSV file
        file_put_contents($csvPath, $csvContent);

        // Create password-protected ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($csvPath, $csvFileName);
            $zip->setPassword($password);
            $zip->setEncryptionName($csvFileName, ZipArchive::EM_AES_256);
            $zip->close();

            // Clean up CSV file
            unlink($csvPath);

            // Send notification
            Notification::make()
                ->title('Export completed')
                ->body('Withdrawal data exported successfully. Found ' . $withdrawals->count() . ' records.')
                ->success()
                ->send();

            // Download the ZIP file
            return Response::download($zipPath, basename($zipPath))->deleteFileAfterSend(true);
        } else {
            Notification::make()
                ->title('Export failed')
                ->body('Unable to create password-protected ZIP file.')
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