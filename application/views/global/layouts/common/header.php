<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Planner</a>
        </div>
        <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo site_url('users/profile/'.$email.''); ?>"><?php echo $email; ?></a></li>    
        <li><a href="<?php echo site_url('users/plannerboard'); ?>">Planner</a></li>
        <li><a href="<?php echo site_url('users/logout'); ?>">Logout</a></li>
        </ul>
        </div>
    </div>
</div>