<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\LocationNode;
use App\Http\Resources\Discover\LocationNodeResource;
use Illuminate\Http\Request;

class LocationHierarchyController extends Controller
{
    public function index(Request $request)
    {
        // Get all top level nodes (Country) with their descendants
        $nodes = LocationNode::with(['children.children', 'children.destinations', 'children.gems'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => LocationNodeResource::collection($nodes),
        ]);
    }
    
    public function show($id)
    {
        $node = LocationNode::with(['children', 'destinations', 'gems'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new LocationNodeResource($node),
        ]);
    }
}
