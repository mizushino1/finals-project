<main style="background-color: #1e1e1e; padding: 50px 20px; font-family: sans-serif; min-height: 100vh;">
    <div class="container" style="max-width: 800px; background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">

        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; color: #333;">Account</h2>

        <form>
            <div class="row mb-4 align-items-center">
                <div class="col-md-3" style="font-weight: bold; color: #555;">Profile Picture</div>
                <div class="col-md-9 d-flex align-items-center gap-3">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background-color: #ccc; border: 2px solid #ddd;"></div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" style="border-color: #ffcc99; color: #555;">Change Profile Picture</button>
                    </div>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">First Name</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" placeholder="First Name" style="border: 1px solid #ffcc99;">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Last Name</label>
                <div class="col-md-9">
                    <input type="text" class="form-control" placeholder="Last Name" style="border: 1px solid #ffcc99;">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Email</label>
                <div class="col-md-9">
                    <input type="email" class="form-control" placeholder="email@example.com" style="border: 1px solid #ffcc99;">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Password</label>
                <div class="col-md-9">
                    <button type="button" class="btn btn-outline-secondary btn-sm" style="border-color: #ffcc99; color: #555;">Change Password</button>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Time Zone</label>
                <div class="col-md-9">
                    <select class="form-select" style="border: 1px solid #ffcc99;">
                        <option>America - Los Angeles</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Development Mode</label>
                <div class="col-md-9">
                    <select class="form-select" style="border: 1px solid #ffcc99;">
                        <option>Default</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Text Editor Mode</label>
                <div class="col-md-9">
                    <select class="form-select" style="border: 1px solid #ffcc99;">
                        <option>Default</option>
                    </select>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <label class="col-md-3" style="font-weight: bold; color: #555;">Subscription Settings</label>
                <div class="col-md-9">
                    <a href="#" style="color: #ffcc99; text-decoration: none;">Manage your email subscription settings.</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9 offset-md-3">
                    <button type="submit" class="btn btn-primary" style="background-color: #ffcc99; border: none; color: #333; padding: 10px 30px; font-weight: bold;">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</main>