@extends("layouts.app")

@section("content")
    <h2 class="mb-3">Account Settings</h2>

    <h3>General Settings</h3>

    <form method="POST" action="{{ route("account.save") }}">
        @csrf
        <div class="row mb-3">
            <label class="col-md-3 col-form-label" for="form-name">
                Name:
            </label>
            <div class="col-md-9">
                <input class="form-control" type="text" id="form-name" name="name" value="{{ $user->name }}">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-3 col-form-label" for="form-name">
                Email:
            </label>
            <div class="col-md-9">
                <input class="form-control" type="text" id="form-name" value="{{ $user->email }}" readonly disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-3 col-form-label">
                E-Mail Notifications:
            </label>
            <div class="col-md-9">
                <p class="mt-2">
                    <input class"form-control" type="checkbox" id="form-notify_archives" name="notify_archives" @checked($user->notify_archives)>
                    <label for="form-notify_archives">
                        Archival request finished
                    </label>
                </p>
                <p>
                    <input class"form-control" type="checkbox" id="form-notify_deposits" name="notify_deposits" @checked($user->notify_deposits)>
                    <label for="form-notify_deposits">
                        Deposit finished
                    </label>
                </p>
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-primary" type="submit">
                Save Changes
            </button>
        </div>
    </form>

    <hr>

    <h3>Change Password</h3>

    <form method="POST" action="{{ route("account.change-password") }}">
        @csrf
        <div class="row mb-3">
            <label class="col-md-3 col-form-label" for="form-old_password">
                Old Password:
            </label>
            <div class="col-md-9">
                <input class="form-control" type="password" id="form-old_password" name="old_password">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-3 col-form-label" for="form-new_password">
                New Password:
            </label>
            <div class="col-md-9">
                <input class="form-control" type="password" id="form-new_password" name="new_password">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-md-3 col-form-label" for="form-new_password_confirmation">
                New Password (confirm):
            </label>
            <div class="col-md-9">
                <input class="form-control" type="password" id="form-new_password_confirmation" name="new_password_confirmation">
            </div>
        </div>
        <div class="text-center">
            <button class="btn btn-primary" type="submit">
                Change Password
            </button>
        </div>
    </form>
@endsection
