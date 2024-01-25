<?php
    if ($session->isLogged()) {
        header("Location: ".$ScriptKaust);
    }

    $username = "";
    $userfullname = "";

    if(isset($_SESSION['post-data']['user_name'])) $username = $_SESSION['post-data']['user_name'];
    if(isset($_SESSION['post-data']['user_fullname'])) $userfullname = $_SESSION['post-data']['user_fullname'];
?>

<section class="vh-50">
    <form action="process.php" method="post">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                        <div class="mb-md-5 mt-md-4 pb-5">
                            <input type="hidden" name="subjoin" value="1" />

                            <h2 class="fw-bold mb-2 text-uppercase">Register</h2>
                            <p class="text-white-50 mb-5">Please enter your login and password!</p>

                            <div class="form-floating form-white mb-4">
                                <input type="text" id="user_name" name="user_name" class="form-control form-control-lg" value="<?php echo $username ;?>"/>
                                <label class="form-label" for="user_name">Username</label>
                            </div>

                            <div class="form-floating form-white mb-4">
                                <input type="text" id="user_fullname" name="user_fullname" class="form-control form-control-lg" value="<?php echo $userfullname ;?>"/>
                                <label class="form-label" for="user_fullname">First & Last name</label>
                            </div>


                            <div class="form-floating form-white mb-4">
                                <input type="password" id="user_password" name="user_password" class="form-control form-control-lg" />
                                <label class="form-label" for="user_password">Password</label>
                            </div>

                            <div class="form-floating form-white mb-4">
                                <input type="password" id="user_password_2" name="user_password_2" class="form-control form-control-lg" />
                                <label class="form-label" for="user_password_2">Repeat Password</label>
                            </div>

                            <button class="btn btn-outline-light btn-lg px-5 mt-3" type="submit">Register</button>
                        </div>

                        <div>
                            <p class="mb-0">Already have an account? <a href="<?php echo $ScriptKaust.'login';?>" class="text-secondary fw-bold">Sign In</a>
                            </p>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>