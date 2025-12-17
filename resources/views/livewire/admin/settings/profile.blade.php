<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $admin = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Admin::class)->ignore($admin->id),
            ],
        ]);

        $admin->fill($validated);

        if ($admin->isDirty('email')) {
            $admin->email_verified_at = null;
        }

        $admin->save();

        $this->dispatch('profile-updated', name: $admin->name);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.admin-layout :heading="__('Profile information')" :subheading="__('Update your account\'s profile information and email address')">
        <form method="POST" wire:submit="updateProfileInformation" class="mt-6 space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                :placeholder="__('Your name')"
                autocomplete="name"
            />

            <flux:input
                wire:model="email"
                :label="__('Email address')"
                :placeholder="__('Your email address')"
                autocomplete="email"
            />

            <div class="flex items-center gap-4">
                <flux:button type="submit" variant="primary" data-test="save-profile-button">{{ __('Save') }}</flux:button>
                <x-action-message class="text-sm text-zinc-600 dark:text-zinc-400" on="profile-updated">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </x-settings.admin-layout>
    <hr class="border-zinc-200 dark:border-zinc-700 my-6" />
</section>
