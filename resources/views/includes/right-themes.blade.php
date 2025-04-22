<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center px-3 py-4">
    
            <h5 class="m-0 me-2">Theme Settings</h5>

            <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="text-center mb-0">Choose Layouts</h6>

        <form action="{{ route('select.theme') }}" method="post">
        @csrf
            <div class="p-4">
                <div class="mb-2">
                    <img src="{{ asset('/assets/images/layouts/layout-1.jpg') }}" class="img-fluid img-thumbnail" alt="">
                </div>

                <div class="form-check form-switch mb-3">
                    <input <?php if (Auth::user()->theme == 'default') {echo "checked";} ?> class="form-check-input" type="radio" name="theme" style="cursor: pointer;" value="default" id="formRadio3">
                    <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                </div>

                <div class="mb-2">
                    <img src="{{ asset('/assets/images/layouts/layout-2.jpg') }}" class="img-fluid img-thumbnail" alt="">
                </div>
                <div class="form-check form-switch mb-3">
                    <input <?php if (Auth::user()->theme == 'dark') {echo "checked";} ?> class="form-check-input" type="radio" name="theme" style="cursor: pointer;" value="dark" id="formRadio3">
                    <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                </div>

                <div class="mt-4" style="text-align: center;">
                    <button type="submit" class="btn btn-dark btn-rounded waves-effect waves-light">Save Theme</button>
                </div>
            </div>
        </form>

    </div> <!-- end slimscroll-menu-->
</div>