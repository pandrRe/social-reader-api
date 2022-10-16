<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;

class ItemsController extends Controller
{
    public function getItems(Request $request) {
        $channelId = $request->query('channelId', null);
        return Channel::findItemsByUserAndId($request->user(), $channelId);
    }
}
