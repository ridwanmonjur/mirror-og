<?php

use Google\Analytics\Admin\V1beta\Client\AnalyticsAdminServiceClient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Google\Analytics\Admin\V1beta\CustomDimension;
use Google\Analytics\Admin\V1beta\CustomDimension\DimensionScope;
use Google\Analytics\Admin\V1beta\CreateCustomDimensionRequest;
use Google\Analytics\Admin\V1beta\ListCustomDimensionsRequest;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createGA4CustomDimensions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->deleteGA4CustomDimensions();
    }

    private function createGA4CustomDimensions(): void
    {
        try {
            // Initialize Google Analytics Admin API client
            $client = new AnalyticsAdminServiceClient([
                'credentials' => base_path(config('analytics.ga4.credentials_path')),
                'scopes' => ['https://www.googleapis.com/auth/analytics.edit']
            ]);

            // Your GA4 Property ID (format: properties/PROPERTY_ID)
            $propertyName = 'properties/' . config('analytics.ga4.property_id');

            // Define custom dimensions based on your tracking code
            $customDimensions = [
                [
                    'display_name' => 'Event ID',
                    'parameter_name' => 'event_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Unique identifier for events'
                ],
                [
                    'display_name' => 'Event Name',
                    'parameter_name' => 'event_name',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Name of the event'
                ],
                [
                    'display_name' => 'Event Tier',
                    'parameter_name' => 'event_tier',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Tier level of events'
                ],
                [
                    'display_name' => 'Event Type',
                    'parameter_name' => 'event_type',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Type/category of event'
                ],
                [
                    'display_name' => 'Esport Title',
                    'parameter_name' => 'esport_title',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Game/esport title'
                ],
                [
                    'display_name' => 'Location',
                    'parameter_name' => 'location',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Event location'
                ],
                [
                    'display_name' => 'User ID',
                    'parameter_name' => 'user_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'User identifier'
                ],
                [
                    'display_name' => 'Session ID',
                    'parameter_name' => 'session_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Custom session identifier'
                ],
                [
                    'display_name' => 'Social Action',
                    'parameter_name' => 'social_action',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Type of social interaction'
                ],
                [
                    'display_name' => 'Social Target',
                    'parameter_name' => 'social_target',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Target of social action'
                ],
                [
                    'display_name' => 'Social Type',
                    'parameter_name' => 'social_type',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Type of social target'
                ],
                [
                    'display_name' => 'Form Name',
                    'parameter_name' => 'form_name',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Name of submitted form'
                ],
                [
                    'display_name' => 'Analytics Version',
                    'parameter_name' => 'analytics_version',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Version of analytics implementation'
                ],
                [
                    'display_name' => 'Error Type',
                    'parameter_name' => 'error_type',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Type of error occurred'
                ],
                [
                    'display_name' => 'Error Message',
                    'parameter_name' => 'error_message',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Error message details'
                ],
                [
                    'display_name' => 'Tier ID',
                    'parameter_name' => 'tier_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Tier identifier'
                ],
                [
                    'display_name' => 'Type ID',
                    'parameter_name' => 'type_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Type identifier'
                ],
                [
                    'display_name' => 'Game ID',
                    'parameter_name' => 'game_id',
                    'scope' => DimensionScope::EVENT,
                    'description' => 'Game identifier'
                ]
            ];

            // Create each custom dimension
            foreach ($customDimensions as $dimensionData) {
                $customDimension = new CustomDimension([
                    'display_name' => $dimensionData['display_name'],
                    'parameter_name' => $dimensionData['parameter_name'],
                    'scope' => $dimensionData['scope'],
                    'description' => $dimensionData['description']
                ]);

                try {
                    $request = new CreateCustomDimensionRequest([
                        'parent' => $propertyName,
                        'custom_dimension' => $customDimension
                    ]);
                    $response = $client->createCustomDimension($request);
                    
                    
                } catch (\Exception $e) {
                    echo "Error creating custom dimension: " . $e->getMessage() . "\n";
                    break;
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function deleteGA4CustomDimensions(): void
    {
        try {
            $client = new AnalyticsAdminServiceClient([
                'credentials' => base_path(config('analytics.ga4.credentials_path')),
                'scopes' => ['https://www.googleapis.com/auth/analytics.edit']
            ]);

            $propertyName = 'properties/' . config('analytics.ga4.property_id');

            // Get all custom dimensions
            $request = new ListCustomDimensionsRequest([
                'parent' => $propertyName
            ]);
            $dimensions = $client->listCustomDimensions($request);

            // Delete dimensions that were created by this migration
            $createdDimensions = [
                'event_id', 'event_name', 'event_tier', 'event_type', 'esport_title', 
                'location', 'user_id', 'session_id', 'social_action', 'social_target', 
                'social_type', 'form_name', 'analytics_version', 'error_type', 
                'error_message', 'tier_id', 'type_id', 'game_id'
            ];

            foreach ($dimensions as $dimension) {
                if (in_array($dimension->getParameterName(), $createdDimensions)) {
                    try {
                        $client->archiveCustomDimension($dimension->getName());
                    } catch (\Exception $e) {
                    }
                }
            }

        } catch (\Exception $e) {
        }
    }
};

