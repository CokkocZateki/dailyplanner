<!doctype html>
<?php include_once 'common/head.php'; ?>
<link rel="stylesheet" href="<?php echo site_url('assets/util/datepicker/datepicker.css'); ?>">

<body class="clearfix">

	<section class="app-wrapper">

	    <?php include_once 'common/header.php'; ?>

	    <div class="content clearfix">
	        <?php $this->load->view($content); ?>
	    </div>

    </section>

    <?php include_once 'common/footer.php'; ?>
    <?php include_once 'common/scripts.php'; ?>
	<script src="<?php echo site_url('assets/util/datepicker/bootstrap-datepicker.js'); ?>"></script>
</body>
</html>