<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->has('is_present')) {
            $query->where('is_present', $request->input('is_present'));
        }

        if ($request->has('is_goalkeeper')) {
            $query->where('is_goalkeeper', $request->input('is_goalkeeper'));
        }

        $query->where('is_deleted', 0);
        $players = $query->paginate(5);

        return response()->json($players);
    }

    public function show($id)
    {
        $player = Player::find($id);

        if ($player) {
            return response()->json($player);
        } else {
            return response()->json(['error' => 'Player not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $player = Player::create($request->all());
        return response()->json($player, 201);
    }

    public function update(Request $request, $id)
    {
        $player = Player::findOrFail($id);
        $player->update($request->all());
        return response()->json($player, 200);
    }

    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->is_deleted = true;
        $player->save();
        return response()->json(null, 204);
    }

    public function sortTeams(Request $request)
    {
        $players = Player::where('is_present', true)->where('is_deleted', false)->get();

        $playersPerTeam = $request->input('players_per_team');
        $totalTeams = floor($players->count() / $playersPerTeam);

        if ($players->count() < $playersPerTeam * 2) {
            return response()->json(['error' => 'Número insuficiente de jogadores confirmados para esta configuração de times.'], 400);
        }

        $goalkeepers = $players->where('is_goalkeeper', true);
        $fieldPlayers = $players->where('is_goalkeeper', false);

        if ($goalkeepers->count() < $totalTeams) {
            return response()->json(['error' => 'Número insuficiente de goleiros para formar os times.'], 400);
        }

        $goalkeepers = $goalkeepers->shuffle();
        $fieldPlayers = $fieldPlayers->shuffle();

        $teams = [];
        for ($i = 0; $i < $totalTeams; $i++) {

            $teams[$i] = [
                'goalkeeper' => $goalkeepers->pop(),
                'players' => $fieldPlayers->splice(0, $playersPerTeam - 1)
            ];
        }

        if ($fieldPlayers->isNotEmpty()) {
            foreach ($fieldPlayers as $player) {
                $teams[array_rand($teams)]['players']->push($player);
            }
        }

        $response = [];
        foreach ($teams as $team) {
            $teamPlayers = $team['players']->prepend($team['goalkeeper']);
            $response[] = $teamPlayers->all();
        }

        return response()->json($response);
    }

    private function generateTeams($players, $playersPerTeam)
    {
        $goalkeepers = $players->where('is_goalkeeper', true);
        $fieldPlayers = $players->where('is_goalkeeper', false);

        $teams = [];
        while ($fieldPlayers->count() >= $playersPerTeam - 1 && $goalkeepers->count() > 0) {
            $team = $goalkeepers->shift();
            $team->players = $fieldPlayers->splice(0, $playersPerTeam - 1);
            $teams[] = $team;
        }

        return $teams;
    }
}
