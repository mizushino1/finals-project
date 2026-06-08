<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="d-flex justify-content-center align-items-center">
    <div class="container-fluid">
        <div class="row my-5 py-5 justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card p-3 my-5" style="border: 3px solid #ffcc80; border-radius: 10px;">
                    <div class="d-flex align-items-start gap-2 my-2 mx-2">

                        <div class="position-relative border border-2 p-5"
                            style="width: 150px; height: 150px; border-radius: 8px;">
                            <i class="bi bi-heart position-absolute" style="top: 5px; right: 5px;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h2 class="mb-0">Bentito</h2>
                                <button class="btn border border-2 btn-sm">View Profile</button>
                            </div>

                            <p class="mb-2"><strong style="font-size: larger;">Description:</strong><br>
                                HAHAHA HAHAHA HAHAHA HAHAHA
                            </p>

                            <div class="mb-3">
                                <p class="mb-0" style="font-size: large;"><strong>Genre:</strong> Anime</p>
                                <p class="mb-0" style="font-size: large;"><strong>Offer:</strong> $500 - $1000</p>
                                <p class="mb-0" style="font-size: large;"><strong>Deadline:</strong> July 28, 2026</p>
                                <p class="mb-0" style="font-size: large;"><strong>Time:</strong> 11:59 pm</p>
                            </div>

                            <div class="d-flex gap-3">
                                <button class="btn btn-success px-5">Accept</button>
                                <button class="btn btn-danger px-5">Decline</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>