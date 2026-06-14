<?php
require_once __DIR__ . '/../../src/middleware/auth_middleware.php';
?>
 <main class="py-4">
     <div class="container-fluid">
         <div class="row pt-3 px-3">
             <div class="col-12">
                 <h3 class="theme-bottom-border">COMMISSIONS</h3>
             </div>

             <div class="col border-bottom border-dark py-4">
                 <div class="row align-items-center">

                     <div class="col d-flex justify-content-end gap-2">
                         <button class="btn btn-fill">
                             Add New Commission
                         </button>
                     </div>
                 </div>
             </div>
         </div>

         <div class="container-fluid px-4 py-4">
             <div class="card theme-fill border-0 p-3">
                 <div class="table-responsive">
                     <table class="table table-borderless align-middle">
                         <select class="form-select theme-fill w-auto" aria-label="Filter by genre">
                             <option selected>Filter by Genre</option>
                             <option value="digital">Digital Art</option>
                             <option value="traditional">Traditional</option>
                             <option value="concept">Concept Art</option>
                             <option value="portrait">Portrait</option>
                         </select>
                         <thead>
                             <tr class="text-muted">
                                 <th scope="col">Hired Artist</th>
                                 <th scope="col">Commission Name</th>
                                 <th scope="col">Genre</th>
                                 <th scope="col">Pricing</th>
                                 <th scope="col">Status</th>
                                 <th scope="col">Action</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr>
                                 <td>
                                     <div class="d-flex align-items-center">
                                         <img src="public/uploads/commissions/placeholder.jpg" width="40" height="40"
                                             class="rounded-circle me-3" alt="Artist">
                                         <span class="fw-bold">Artist Name</span>
                                     </div>
                                 </td>
                                 <td>Commission Names</td>
                                 <td>Anime</td>
                                 <td>$100,000</td>
                                 <td>
                                     <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                 </td>
                                 <td><i class="btn btn-fill">Edit</td>
                             </tr>
                         </tbody>
                     </table>
                 </div>

                 <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                     <button class="btn btn-fill">Previous</button>
                     <nav>
                         <ul class="pagination mb-0 gap-2">
                             <li class="page-item"><a class="page-link border-0 rounded" href="#">01</a></li>
                             <li class="page-item"><a class="page-link border-0 rounded" href="#">02</a></li>
                             <li class="page-item"><a class="page-link border-0 rounded" href="#">03</a></li>
                             <li class="page-item"><a class="page-link border-0 rounded" href="#">04</a></li>
                             <li class="page-item"><span class="page-link border-0">...</span></li>
                             <li class="page-item"><a class="page-link border-0 rounded" href="#">10</a></li>
                         </ul>
                     </nav>
                     <button class="btn btn-fill">Next</button>
                 </div>
             </div>
         </div>
     </div>
 </main>