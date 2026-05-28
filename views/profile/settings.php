<main class="py-5">
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="row justify-content-center">
            <div class="col-12" style="background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">

                <h2 style="border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; color: #333;">Account Settings</h2>

                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="theme-border" style="padding: 20px; margin-bottom: 20px;">
                                <h5 style="color: #333; margin-bottom: 15px;"><i class="bi bi-person"></i> Personal Information</h5>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                            <input type="text" class="form-control" placeholder="Username">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Edit Bio</label>
                                    <div class="input-group">
                                        <textarea class="form-control" rows="2" placeholder="Bio..."></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="theme-border" style="padding: 20px;">
                                <h5 style="color: #333; margin-bottom: 15px;"><i class="bi bi-lock"></i> Security</h5>
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" placeholder="••••••••">
                                        <span class="input-group-text"><i class="bi bi-eye"></i></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control">
                                            <span class="input-group-text"><i class="bi bi-eye"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Confirm</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control">
                                            <span class="input-group-text"><i class="bi bi-eye"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="theme-border" style=" padding: 20px; margin-bottom: 20px;">
                                <h5 style="margin-bottom: 15px;"><i class="bi bi-person-circle"></i> Avatar / Profile Picture</h5>

                                <div class="row align-items-center">
                                    <div class="col-5 text-center">
                                        <div style="width: 100px; height: 100px; background: #eee; border-radius: 50%; margin: 0 auto 10px auto; border: 2px solid #ccc;"></div>
                                        <button type="button" class="btn btn-outline btn-sm w-100">Remove Avatar</button>
                                    </div>

                                    <div class="col-7 text-center">
                                        <div class="p-3" style="border: 1px dashed #aaa; border-radius: 8px; background: #fafafa;">
                                            <p style="font-size: 0.85rem; margin-bottom: 10px;">
                                                <strong>Choose New Avatar</strong><br>
                                                Drag & drop an image here or click browse here
                                            </p>
                                            <button type="button" class="btn btn-sm btn-fill fw-bold w-100" >
                                                <i class="bi bi-upload"></i> Select Image
                                            </button>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Recommended PNG</small>
                                    </div>
                                </div>
                            </div>

                            <div style="border: 1px solid #ffcc99; border-radius: 10px; padding: 20px;">
                                <h5 style="color: #333; margin-bottom: 15px;"><i class="bi bi-pencil-square"></i> Artist Description</h5>
                                <textarea class="form-control" rows="6" placeholder="Tell us about your art..."></textarea>
                                <small class="text-muted d-block mt-1 text-end">0/250</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center">
                        <div class="col-6 d-flex gap-2">
                            <button type="submit" class="btn btn-fill">Save Changes</button>
                            <button type="button" class="btn btn-outline">Cancel</button>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>