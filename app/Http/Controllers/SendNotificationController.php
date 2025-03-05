<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\NotificationEvent;
class SendNotificationController extends Controller
{
    public function sendNotification(){
        try {
            $message = 'Hello, my name is John Doe';
            
            // Vérifier la configuration de Pusher avant d'envoyer
            if (!config('broadcasting.connections.pusher.key') || 
                !config('broadcasting.connections.pusher.secret') || 
                !config('broadcasting.connections.pusher.app_id')) {
                throw new \Exception('Configuration Pusher manquante');
            }
            
            event(new NotificationEvent($message));
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Pusher\PusherException $e) {
            \Log::error('Erreur Pusher: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur de connexion Pusher',
                'error' => $e->getMessage()
            ], 503);
        } catch (\Exception $e) {
            \Log::error('Erreur de notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Échec de l\'envoi de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
