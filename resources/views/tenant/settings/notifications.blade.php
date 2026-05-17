@extends('layouts.app')

@section('title', 'Notification Settings - ' . ($tenant->name ?? 'Gaming Zone'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Notification Settings</h1>
        <p class="text-gray-600 mt-2">Configure how you receive notifications for bookings and events.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('tenant.settings.notifications.update') }}" method="POST">
                @csrf
                
                <!-- Email Notifications Toggle -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Email Notifications</h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_enabled" 
                                   value="1" 
                                   {{ $emailEnabled ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-gray-700 font-medium">
                                Enable Email Notifications
                            </span>
                        </label>
                        <p class="text-sm text-gray-500 mt-2 ml-8">
                            When enabled, users will receive email notifications based on the settings below.
                        </p>
                    </div>
                </div>

                <!-- Notification Types -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Notification Types</h2>
                    <p class="text-sm text-gray-600 mb-4">Select which events should trigger email notifications:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Booking Notifications -->
                        <div class="space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Booking Notifications</h3>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[booking_created]" 
                                       value="1" 
                                       {{ isset($notificationTypes['booking_created']) && $notificationTypes['booking_created'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">New booking created</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[booking_approved]" 
                                       value="1" 
                                       {{ isset($notificationTypes['booking_approved']) && $notificationTypes['booking_approved'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Booking approved</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[booking_rejected]" 
                                       value="1" 
                                       {{ isset($notificationTypes['booking_rejected']) && $notificationTypes['booking_rejected'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Booking rejected</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[booking_cancelled]" 
                                       value="1" 
                                       {{ isset($notificationTypes['booking_cancelled']) && $notificationTypes['booking_cancelled'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Booking cancelled</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[booking_completed]" 
                                       value="1" 
                                       {{ isset($notificationTypes['booking_completed']) && $notificationTypes['booking_completed'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Booking completed</span>
                            </label>
                        </div>

                        <!-- Payment Notifications -->
                        <div class="space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Payment Notifications</h3>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[payment_received]" 
                                       value="1" 
                                       {{ isset($notificationTypes['payment_received']) && $notificationTypes['payment_received'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Payment received</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="notification_types[payment_refunded]" 
                                       value="1" 
                                       {{ isset($notificationTypes['payment_refunded']) && $notificationTypes['payment_refunded'] ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-gray-600 text-sm">Payment refunded</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> In-app notifications are always sent regardless of these settings. 
                    Email notifications require proper mail configuration in your .env file.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
