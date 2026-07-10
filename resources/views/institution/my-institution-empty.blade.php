@extends('layouts.app')

@section('title', 'No Institution')
@section('page-title', 'No Institution')

@section('content')

<div style="position: fixed; inset: 0; background: linear-gradient(135deg, #1e293b, #0f172a, #312e81); z-index: -10;"></div>

<div style="position: relative; z-index: 10; min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; max-width: 28rem; margin: 0 auto; padding: 0 1rem;">
        <div style="background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 1rem; padding: 2rem; border: 1px solid rgba(255,255,255,0.08);">
            
            <!-- Icon -->
            <div style="width: 5rem; height: 5rem; background: linear-gradient(135deg, #7c3aed, #ec4899); border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="ti ti-building-community" style="font-size: 1.75rem; color: #ffffff;"></i>
            </div>
            
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin-bottom: 0.5rem;">No Institution Yet</h2>
            <p style="color: #d1d5db; margin-bottom: 1.5rem;">You haven't joined any institution. Discover and join one to access exclusive resources!</p>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem; justify-content: center;">
                <a href="{{ route('discover.institutions') }}" 
                   style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #7c3aed, #db2777); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.2s; text-decoration: none;">
                    <i class="ti ti-building-community"></i> Discover Institutions
                </a>
                <a href="{{ route('institution.create-request') }}" 
                   style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: rgba(255,255,255,0.08); color: white; padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.2s; text-decoration: none; border: 1px solid rgba(255,255,255,0.05);">
                    <i class="ti ti-file-plus"></i> Create Institution
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* ========================================== */
    /* CLEAN NO INSTITUTION STYLES                */
    /* ========================================== */

    a[style*="Discover Institutions"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
        filter: brightness(1.05);
    }
    
    a[style*="Create Institution"]:hover {
        background: rgba(255,255,255,0.15) !important;
        border-color: rgba(255,255,255,0.15) !important;
        transform: translateY(-2px);
    }
    
    /* Card hover */
    div[style*="background: rgba(255,255,255,0.08)"] {
        transition: all 0.3s ease;
    }
    
    div[style*="background: rgba(255,255,255,0.08)"]:hover {
        background: rgba(255,255,255,0.1) !important;
        border-color: rgba(255,255,255,0.12) !important;
    }
</style>

@endsection