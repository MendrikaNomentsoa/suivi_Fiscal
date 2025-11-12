<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::with('contribuable', 'agent')->get();

        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'design' => 'nullable|string|',
            'date_envoi' => 'required|date',
            'date_lecture' => 'required|date',
            'statut' => 'required|string|max:50',
            'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
            'id_Agent' => 'required|exists:agents,id_Agent',

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_Notif)
    {
        $notification = Notification::with('contribuable')->find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => 'notifi$notification non trouvé'], 404);
        }

        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_Notif)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_Notif)
    {
        $notification = Notification::find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => '$notification non trouvé'], 404);
        }

        $notification->delete();

        return response()->json(['message' => '$notification supprimé avec succès']);
    }
}
