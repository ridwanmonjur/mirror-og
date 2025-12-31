<?php

namespace Tests\Mocks;

use Mockery;

trait MocksSteamAuth
{
    /**
     * Mock Steam authentication
     *
     * @param string $steamId Steam 64-bit ID
     * @param string $personaName Steam display name
     * @return object Mocked Steam user
     */
    protected function mockSteamAuth($steamId = '76561198000000000', $personaName = 'TestSteamUser')
    {
        // Mock Steam API response
        $steamUser = (object) [
            'steamid' => $steamId,
            'personaname' => $personaName,
            'profileurl' => "https://steamcommunity.com/id/{$personaName}",
            'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/test.jpg',
            'avatarmedium' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/test_medium.jpg',
            'avatarfull' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/test_full.jpg',
        ];

        return $steamUser;
    }

    /**
     * Mock Steam OpenID validation
     */
    protected function mockSteamOpenIdValidation($isValid = true, $steamId = '76561198000000000')
    {
        // This would mock the Steam OpenID verification process
        // Implementation depends on how Steam auth is integrated
        return [
            'valid' => $isValid,
            'steamid' => $steamId,
        ];
    }
}
