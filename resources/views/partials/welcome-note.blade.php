<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
    <div>
        <div class="text-xl font-semibold">
            Congratulations {{ auth('admin')->user()->name ?? 'Admin' }}! 🎉
        </div>
        <p class="mt-2 text-sm text-gray-600">You have done 72% more sales today.</p>
        <p class="text-sm text-gray-600">Check your new badge in your profile.</p>
    </div>
    <div class="hidden md:block">
        <svg width="180" height="120" viewBox="0 0 180 120" xmlns="http://www.w3.org/2000/svg">
            <rect x="120" y="70" width="40" height="22" rx="4" fill="#D1D5DB"/>
            <circle cx="140" cy="40" r="18" fill="#A5B4FC"/>
            <rect x="20" y="30" width="60" height="42" rx="6" fill="#F3F4F6"/>
            <rect x="26" y="36" width="48" height="6" rx="3" fill="#D1D5DB"/>
            <rect x="26" y="46" width="40" height="6" rx="3" fill="#D1D5DB"/>
            <rect x="26" y="56" width="32" height="6" rx="3" fill="#D1D5DB"/>
            <rect x="80" y="86" width="20" height="6" rx="3" fill="#E5E7EB"/>
            <rect x="105" y="86" width="20" height="6" rx="3" fill="#E5E7EB"/>
        </svg>
    </div>
</div>
