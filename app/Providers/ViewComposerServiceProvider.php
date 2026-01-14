<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class ViewComposerServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer('partials.topbar', function ($view) {
            if (Auth::check()) {
                try {
                    $user = Auth::user();
                    $clearedAt = session('notifications_cleared_at');
                    
                    $notifications = collect(); 
                    $unreadNotifications = 0;
                    
                    if (class_exists('App\\Models\\ActivityLog')) {
                        $query = ActivityLog::with('user')
                            ->where('user_id', '!=', $user->id); 
                        
                        if ($clearedAt) {
                            $query = $query->where('created_at', '>', $clearedAt);
                        }
                        
                        $notifications = $query
                            ->latest()
                            ->take(5)
                            ->get()
                            ->map(function ($activity) {
                                return [
                                    'title' => ucfirst(str_replace('_', ' ', $activity->type)),
                                    'message' => $activity->description,
                                    'time' => $activity->created_at->diffForHumans(),
                                    'icon' => $this->getIconForActivityType($activity->type),
                                ];
                            });

                        $unreadNotifications = $notifications->count();
                    }

                    $view->with(compact('notifications', 'unreadNotifications'));
                } catch (\Exception $e) {
                    $view->with([
                        'notifications' => collect(),
                        'unreadNotifications' => 0
                    ]);
                }
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotifications' => 0
                ]);
            }
        });
    }

    private function getIconForActivityType($type)
    {
        return match($type) {
            'book_added' => 'book',
            'event_created' => 'calendar',
            'event_deleted' => 'calendar-times',
            'user_registered' => 'user-plus',
            default => 'info-circle',
        };
    }
}
