@extends('layouts.librarian')

@section('title', 'Settings')
@section('page-title', '⚙️ Settings')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <p class="text-purple-300/60 text-sm">Manage your librarian preferences</p>
    </div>

    <div class="bg-white/5 backdrop-blur-xl rounded-xl shadow-sm overflow-hidden border border-purple-500/20">
        <div class="px-6 py-4 border-b border-purple-500/20 bg-purple-900/10">
            <h3 class="font-semibold text-white">General Settings</h3>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('librarian.settings.update') }}">
                @csrf
                @method('PUT')
                
                <!-- Notification Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-purple-300">Notifications</h4>
                    
                    <div class="flex items-center justify-between py-2 border-b border-purple-500/10">
                        <div>
                            <p class="text-sm font-medium text-white">Email Notifications</p>
                            <p class="text-xs text-purple-300/50">Receive email updates about library activity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-purple-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between py-2 border-b border-purple-500/10">
                        <div>
                            <p class="text-sm font-medium text-white">Book Approval Alerts</p>
                            <p class="text-xs text-purple-300/50">Get notified when new books need approval</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="approval_alerts" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-purple-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between py-2 border-b border-purple-500/10">
                        <div>
                            <p class="text-sm font-medium text-white">Member Activity Reports</p>
                            <p class="text-xs text-purple-300/50">Receive weekly member activity summaries</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="member_reports" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-purple-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-purple-500/10">
                        <div>
                            <p class="text-sm font-medium text-white">New Member Notifications</p>
                            <p class="text-xs text-purple-300/50">Get notified when new members join</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="new_member_alerts" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:ring-2 peer-focus:ring-purple-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                        </label>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="mt-6 pt-6 border-t border-purple-500/10">
                    <h4 class="font-medium text-purple-300">Display Preferences</h4>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Default Book View</label>
                            <select name="default_view" class="w-full px-4 py-2 bg-white/5 border border-purple-500/20 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                                <option value="grid" class="text-gray-800">Grid View</option>
                                <option value="list" class="text-gray-800">List View</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Items Per Page</label>
                            <select name="per_page" class="w-full px-4 py-2 bg-white/5 border border-purple-500/20 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                                <option value="15" class="text-gray-800">15</option>
                                <option value="25" class="text-gray-800">25</option>
                                <option value="50" class="text-gray-800">50</option>
                                <option value="100" class="text-gray-800">100</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Submit -->
                <div class="mt-6 pt-6 border-t border-purple-500/10 flex gap-3">
                    <button type="submit" class="btn-library">
                        <i class="ti ti-device-floppy"></i> Save Settings
                    </button>
                    <a href="{{ route('librarian.dashboard') }}" class="btn-library-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Back to Dashboard -->
    <div class="mt-4">
        <a href="{{ route('librarian.dashboard') }}" class="text-purple-400 hover:text-purple-300 text-sm inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

@endsection