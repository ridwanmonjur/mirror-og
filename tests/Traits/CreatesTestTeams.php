<?php

namespace Tests\Traits;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamCaptain;
use App\Models\TeamProfile;

trait CreatesTestTeams
{
    /**
     * Create a basic team
     */
    protected function createTeam(array $attributes = [], $creator = null)
    {
        if (!$creator) {
            $creator = $this->createParticipant();
        }

        $team = Team::factory()->create(array_merge([
            'creator_id' => $creator->id,
            'member_limit' => 5,
        ], $attributes));

        // Create team captain
        TeamCaptain::factory()->create([
            'teams_id' => $team->id,
            'user_id' => $creator->id,
        ]);

        // Create team profile if doesn't exist
        if (!$team->profile) {
            TeamProfile::factory()->create([
                'team_id' => $team->id,
            ]);
        }

        return $team->fresh(['captain', 'members', 'profile']);
    }

    /**
     * Create a team with members
     */
    protected function createTeamWithMembers($memberCount = 3, $creator = null, array $teamAttributes = [])
    {
        $team = $this->createTeam($teamAttributes, $creator);

        for ($i = 0; $i < $memberCount; $i++) {
            $member = $this->createParticipant();

            TeamMember::factory()->create([
                'team_id' => $team->id,
                'user_id' => $member->id,
                'status' => 'approved',
            ]);
        }

        return $team->fresh(['members', 'captain']);
    }

    /**
     * Create a team with pending members
     */
    protected function createTeamWithPendingMembers($pendingCount = 2, $creator = null)
    {
        $team = $this->createTeam([], $creator);

        for ($i = 0; $i < $pendingCount; $i++) {
            $member = $this->createParticipant();

            TeamMember::factory()->create([
                'team_id' => $team->id,
                'user_id' => $member->id,
                'status' => 'pending',
            ]);
        }

        return $team->fresh(['members']);
    }

    /**
     * Create a full team (at member limit)
     */
    protected function createFullTeam($memberLimit = 5, $creator = null)
    {
        $team = $this->createTeam(['member_limit' => $memberLimit], $creator);

        // Member limit - 1 because creator/captain counts as 1
        for ($i = 0; $i < $memberLimit - 1; $i++) {
            $member = $this->createParticipant();

            TeamMember::factory()->create([
                'team_id' => $team->id,
                'user_id' => $member->id,
                'status' => 'approved',
            ]);
        }

        return $team->fresh(['members', 'captain']);
    }

    /**
     * Add member to team
     */
    protected function addTeamMember(Team $team, $user = null, $status = 'approved')
    {
        if (!$user) {
            $user = $this->createParticipant();
        }

        return TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => $status,
        ]);
    }

    /**
     * Create multiple teams
     */
    protected function createTeams($count = 3, array $attributes = [], $creator = null)
    {
        $teams = [];

        for ($i = 0; $i < $count; $i++) {
            $teams[] = $this->createTeam($attributes, $creator);
        }

        return collect($teams);
    }

    /**
     * Create teams with members for tournament
     */
    protected function createTeamsForTournament($teamCount = 8, $membersPerTeam = 5)
    {
        $teams = [];

        for ($i = 0; $i < $teamCount; $i++) {
            $teams[] = $this->createTeamWithMembers($membersPerTeam);
        }

        return collect($teams);
    }
}
