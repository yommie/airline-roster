<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\Api\V1\RosterActivitiesFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UploadRosterRequest;
use App\Http\Resources\Api\V1\DayActivityCollection;
use App\Jobs\ImportRosterJob;
use App\Models\DayActivity;
use App\Services\Roster\Factories\ParseRosterFactory;
use App\Services\Roster\RosterParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RosterController extends Controller
{
    public function uploadRoster(UploadRosterRequest $request): JsonResponse
    {
        $roster = $request->file("roster");

        $rosterFormat = $roster->getClientOriginalExtension();

        $parser = ParseRosterFactory::createRosterParser($rosterFormat);

        $rosterActivities = (new RosterParser($parser))->parse($roster->getContent());

        dispatch(new ImportRosterJob($rosterActivities));

        return response()->json([
            "message" => "Imported rosters"
        ], Response::HTTP_CREATED);
    }

    public function getActivities(Request $request): DayActivityCollection
    {
        $activities = RosterActivitiesFilter::apply(
            DayActivity::query(),
            $request->all()
        )->paginate($request->query("per_page"));

        return new DayActivityCollection($activities);
    }
}
