@extends('layouts.librarian')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

<div class="max-w-4xl mx-auto" style="background: #fff8f0; padding: 1.5rem; border-radius: 1.5rem;">
    
    <div class="mb-6">
        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Manage your librarian preferences</p>
    </div>

    <div style="background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(30, 58, 95, 0.1); border-radius: 1rem; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.06);">
        
        <!-- Header -->
        <div style="padding: 0.75rem 1.5rem; border-bottom: 1px solid rgba(30, 58, 95, 0.08); background: rgba(30, 58, 95, 0.03);">
            <h3 style="font-weight: 600; color: #1a1a2e; margin: 0; font-size: 1rem;">General Settings</h3>
        </div>
        
        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('librarian.settings.update') }}">
                @csrf
                @method('PUT')
                
                <!-- ========================================== -->
                <!-- NOTIFICATION SETTINGS                      -->
                <!-- ========================================== -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <h4 style="font-weight: 600; color: #1e3a5f; margin: 0; font-size: 0.95rem;">Notifications</h4>
                    
                    <!-- Email Notifications -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 500; color: #1a1a2e; margin: 0;">Email Notifications</p>
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">Receive email updates about library activity</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="email_notifications" value="1" checked class="sr-only peer" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                            <div style="width: 2.75rem; height: 1.5rem; background: #d6d2cb; border-radius: 9999px; transition: all 0.2s; position: relative; cursor: pointer;">
                                <div style="content: ''; position: absolute; top: 0.1rem; left: 0.1rem; background: white; border-radius: 9999px; height: 1.2rem; width: 1.2rem; transition: all 0.2s; transform: translateX(0);"></div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Book Approval Alerts -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 500; color: #1a1a2e; margin: 0;">Book Approval Alerts</p>
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">Get notified when new books need approval</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="approval_alerts" value="1" checked class="sr-only peer" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                            <div style="width: 2.75rem; height: 1.5rem; background: #d6d2cb; border-radius: 9999px; transition: all 0.2s; position: relative; cursor: pointer;">
                                <div style="content: ''; position: absolute; top: 0.1rem; left: 0.1rem; background: white; border-radius: 9999px; height: 1.2rem; width: 1.2rem; transition: all 0.2s; transform: translateX(0);"></div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Member Activity Reports -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 500; color: #1a1a2e; margin: 0;">Member Activity Reports</p>
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">Receive weekly member activity summaries</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="member_reports" value="1" class="sr-only peer" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                            <div style="width: 2.75rem; height: 1.5rem; background: #d6d2cb; border-radius: 9999px; transition: all 0.2s; position: relative; cursor: pointer;">
                                <div style="content: ''; position: absolute; top: 0.1rem; left: 0.1rem; background: white; border-radius: 9999px; height: 1.2rem; width: 1.2rem; transition: all 0.2s; transform: translateX(0);"></div>
                            </div>
                        </label>
                    </div>

                    <!-- New Member Notifications -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(30, 58, 95, 0.06);">
                        <div>
                            <p style="font-size: 0.875rem; font-weight: 500; color: #1a1a2e; margin: 0;">New Member Notifications</p>
                            <p style="font-size: 0.7rem; color: #6b7280; margin: 0;">Get notified when new members join</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="new_member_alerts" value="1" class="sr-only peer" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                            <div style="width: 2.75rem; height: 1.5rem; background: #d6d2cb; border-radius: 9999px; transition: all 0.2s; position: relative; cursor: pointer;">
                                <div style="content: ''; position: absolute; top: 0.1rem; left: 0.1rem; background: white; border-radius: 9999px; height: 1.2rem; width: 1.2rem; transition: all 0.2s; transform: translateX(0);"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- DISPLAY SETTINGS                           -->
                <!-- ========================================== -->
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(30, 58, 95, 0.06);">
                    <h4 style="font-weight: 600; color: #1e3a5f; margin-bottom: 1rem; font-size: 0.95rem;">Display Preferences</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #1a1a2e; margin-bottom: 0.4rem;">Default Book View</label>
                            <select name="default_view" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="grid" style="color: #1a1a2e;">Grid View</option>
                                <option value="list" style="color: #1a1a2e;">List View</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 500; color: #1a1a2e; margin-bottom: 0.4rem;">Items Per Page</label>
                            <select name="per_page" style="width: 100%; padding: 0.6rem 2.5rem 0.6rem 1rem; background: #faf8f5; border: 1px solid #e2e0db; border-radius: 0.5rem; color: #1a1a2e; transition: all 0.2s; outline: none; font-size: 0.875rem; appearance: none; background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E\"); background-repeat: no-repeat; background-position: right 1rem center; cursor: pointer;">
                                <option value="15" style="color: #1a1a2e;">15</option>
                                <option value="25" style="color: #1a1a2e;">25</option>
                                <option value="50" style="color: #1a1a2e;">50</option>
                                <option value="100" style="color: #1a1a2e;">100</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- ========================================== -->
                <!-- FORM ACTIONS                               -->
                <!-- ========================================== -->
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(30, 58, 95, 0.06);">
                    <button type="submit" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #db570a; color: white; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem; transition: all 0.2s; cursor: pointer;">
                        <i class="ti ti-device-floppy"></i> Save Settings
                    </button>
                    <a href="{{ route('librarian.dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: #faf8f5; color: #475569; border: 1px solid #e2e0db; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; transition: all 0.2s; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Back to Dashboard -->
    <div style="margin-top: 1rem;">
        <a href="{{ route('librarian.dashboard') }}" style="color: #5b21b6; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none; transition: color 0.2s;">
            <i class="ti ti-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

<style>
    /* ========================================== */
    /* 1px DIM DARK BLUE BORDER STYLES            */
    /* ========================================== */

    a[style*="Back to Dashboard"]:hover {
        color: #4c1d95 !important;
    }
    
    input:focus, 
    select:focus {
        border-color: #db570a !important;
        box-shadow: 0 0 0 3px rgba(219, 87, 10, 0.08) !important;
        background: white !important;
    }
    
    input:hover, 
    select:hover {
        border-color: #c5c0b8 !important;
        background: white !important;
    }
    
    /* Save button hover */
    button[type="submit"]:hover {
        background: #c44a08 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(219, 87, 10, 0.3);
    }
    
    /* Cancel button hover */
    a[style*="Cancel"]:hover {
        border-color: #db570a !important;
        background: white !important;
        color: #1a1a2e !important;
    }
    
    /* Main card hover */
    div[style*="background: rgba(255,255,255,0.85)"] {
        transition: all 0.2s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.85)"]:hover {
        box-shadow: 0 4px 16px rgba(30, 58, 95, 0.04) !important;
    }
    
    /* Toggle switch - checked state */
    input[type="checkbox"]:checked + div {
        background: #1e3a5f !important;
    }
    
    input[type="checkbox"]:checked + div > div {
        transform: translateX(1.25rem) !important;
    }
    
    /* Toggle switch hover */
    label:hover div {
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1);
    }
    
    @media (max-width: 768px) {
        div[style*="display: grid; grid-template-columns: 1fr 1fr;"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; gap: 0.75rem; margin-top: 1.5rem;"] {
            flex-direction: column !important;
        }
        
        button[type="submit"],
        a[style*="Cancel"] {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

@endsection