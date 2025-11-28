<style>
    #editModalContent {
        max-height: 10px;
        overflow-y: auto;
    }
</style>

<div class="modal fade" id="editEmployeeModal<?php echo $row['emp_id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="editModalContent">
                <form id="editEmployeeForm" enctype="multipart/form-data">

                    <input type="hidden" id="emp_id" name="emp_id">

                    <div class="row">

                        <!-- LEFT SIDE -->
                        <div class="col-md-6">
                            <h5>Personal Information</h5>

                            <label>ID Number:</label>
                            <input type="text" id="id_number" name="id_number" class="form-control" required>

                            <label>First Name:</label>
                            <input type="text" id="fname" name="fname" class="form-control" required>

                            <label>Middle Name:</label>
                            <input type="text" id="mname" name="mname" class="form-control">

                            <label>Last Name:</label>
                            <input type="text" id="lname" name="lname" class="form-control" required>

                            <label>Position:</label>
                            <input type="text" id="position" name="position" class="form-control">
                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="col-md-6">
                            <h5>Location & Contact</h5>

                            <label>Office:</label>
                            <input type="text" id="office" name="office" class="form-control" required>

                            <label>Campus:</label>
                            <input type="text" id="campus" name="campus" class="form-control" required>

                            <label>Province:</label>
                            <input type="text" id="province" name="province" class="form-control" required>

                            <label>City:</label>
                            <input type="text" id="city" name="city" class="form-control" required>

                            <label>Barangay:</label>
                            <input type="text" id="barangay" name="barangay" class="form-control" required>
                        </div>

                        <!-- EMERGENCY DETAILS -->
                        <div class="col-md-12 mt-3">
                            <h5>Emergency Contact</h5>

                            <label>Contact Person:</label>
                            <input type="text" id="emc_person" name="emc_person" class="form-control" required>

                            <label>Contact Number:</label>
                            <input type="text" id="emc_number" name="emc_number" class="form-control" required>

                            <label>Address:</label>
                            <input type="text" id="emc_address" name="emc_address" class="form-control" required>

                            <h5>Employee Photo</h5>
                            <label>Employee Image:</label>
                            
                            <input type="file" class="form-control" id="emp_image" name="emp_image" accept="image/*">
                            
                            <div class="mt-2 text-center">
                                <img id="preview_image" src="" class="img-fluid rounded" style="max-width: 200px; display: none;">
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="text-end mt-3 p-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editEmployeeForm" class="btn btn-primary">Save Changes</button>
            </div>

        </div>
    </div>
</div>
