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
        $notifications = Notification::with('contribuable', 'agent')
            ->orderBy('date_envoi', 'desc')
            ->get();
        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'design' => 'nullable|string',
            'date_envoi' => 'required|date',
            'date_lecture' => 'nullable|date',
            'statut' => 'required|string|max:50',
            'id_Contribuable' => 'required|exists:contribuables,id_Contribuable',
            'id_Agent' => 'nullable|exists:agents,id_Agent',
        ]);

        $notification = Notification::create($data);

        return response()->json([
            'message' => 'Notification créée avec succès',
            'notification' => $notification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_Notif)
    {
        $notification = Notification::with('contribuable', 'agent')->find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => 'Notification non trouvée'], 404);
        }

        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_Notif)
    {
        $notification = Notification::find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => 'Notification non trouvée'], 404);
        }

        $data = $request->validate([
            'design' => 'nullable|string',
            'date_envoi' => 'sometimes|date',
            'date_lecture' => 'nullable|date',
            'statut' => 'sometimes|string|max:50',
            'id_Contribuable' => 'sometimes|exists:contribuables,id_Contribuable',
            'id_Agent' => 'nullable|exists:agents,id_Agent',
        ]);

        $notification->update($data);

        return response()->json([
            'message' => 'Notification mise à jour avec succès',
            'notification' => $notification
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $id_Notif)
    {
        $notification = Notification::find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => 'Notification non trouvée'], 404);
        }

        $notification->update([
            'date_lecture' => now(),
            'statut' => 'lu'
        ]);

        return response()->json([
            'message' => 'Notification marquée comme lue',
            'notification' => $notification
        ]);
    }

    /**
     * Get notifications for a specific contribuable
     */
    public function getByContribuable(string $id_Contribuable)
    {
        $notifications = Notification::with('agent')
            ->where('id_Contribuable', $id_Contribuable)
            ->orderBy('date_envoi', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count for a contribuable
     */
    public function getUnreadCount(string $id_Contribuable)
    {
        $count = Notification::where('id_Contribuable', $id_Contribuable)
            ->where('statut', 'non lu')
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_Notif)
    {
        $notification = Notification::find($id_Notif);

        if (!$notification) {
            return response()->json(['message' => 'Notification non trouvée'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée avec succès']);
    }
}
