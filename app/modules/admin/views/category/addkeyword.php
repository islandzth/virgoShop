<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<?php include_once(__SITE_FOLDER . 'views/admin/header.php') ?>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="fixed-top">
<!-- BEGIN HEADER -->
<?php include_once(__SITE_FOLDER . 'views/admin/headerbar.php') ?>
<!-- END HEADER -->
<!-- BEGIN CONTAINER -->
<div id="container" class="row-fluid">
    <!-- BEGIN SIDEBAR -->
    <div id="sidebar" class="nav-collapse collapse">
        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        <div class="sidebar-toggler hidden-phone"></div>
        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->

        <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
        <div class="navbar-inverse">
            <form class="navbar-search visible-phone">
                <input type="text" class="search-query" placeholder="Search"/>
            </form>
        </div>
        <!-- END RESPONSIVE QUICK SEARCH FORM -->
        <?php
        include_once(__SITE_FOLDER . 'views/admin/sidebar.php');
        ?>
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN PAGE -->
    <div id="main-content">
        <!-- BEGIN PAGE CONTAINER-->
        <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN THEME CUSTOMIZER-->
                    <div id="theme-change" class="hidden-phone">
                        <i class="icon-cogs"></i> <span class="settings"> <span class="text">Theme:</span> <span
                                class="colors"> <span class="color-default" data-style="default"></span> <span
                                    class="color-gray"
                                    data-style="gray"></span> <span class="color-purple"
                                                                    data-style="purple"></span> <span
                                    class="color-navy-blue" data-style="navy-blue"></span> </span> </span>
                    </div>
                    <!-- END THEME CUSTOMIZER-->
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Danh mục</h3>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row-fluid">
                <div class="span12">
                    <!-- BEGIN BORDERED TABLE widget-->
                    <div class="widget">
                        <div class="widget-title">
                            <h4>
                                <i class="icon-reorder"></i>Từ khóa cho danh mục
                                <?= $this->cat['category_name'] ?>
                            </h4>
                            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
                        </div>
                        <div class="widget-body">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Keyword</th>
                                    <th>Điều chỉnh</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($this->keywordlist) {
                                    foreach ($this->keywordlist as $k) {
                                        echo '<tr>
											<td>' . $k['id'] . '</td>
											<td>' . $k['category_keyword'] . '</td>
											<td><a href="' . ADMIN_URL . 'category/deleteKeyword/' . $k['id'] . '?cid=' . $k['category_id'] . '">Xóa</a></td>
										</tr>';
                                    }

                                }
                                ?>
                                </tbody>
                            </table>
                            <br/>

                            <form action="" method="post" class="form-horizontal">
                                <input type="hidden" name="do" value="add"/>

                                <div class="control-group">
                                    <label class="control-label">Từ khóa</label>

                                    <div class="controls">
                                        <input type="text" name="keyword"/>
                                    </div>
                                </div>


                                <div class="form-actions">
                                    <button type="submit" class="btn blue">
                                        <i class="icon-ok"></i> Thêm từ khóa
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- END BORDERED TABLE widget-->
                </div>
            </div>
        </div>

        <!-- END PAGE CONTENT-->
    </div>
    <!-- END PAGE CONTAINER-->
</div>
<!-- END PAGE -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div id="footer">
    2013 &copy; Admin Lab Dashboard.
    <div class="span pull-right">
        <span class="go-top"><i class="icon-arrow-up"></i> </span>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS -->
<!-- Load javascripts at bottom, this will reduce page load time -->
<script src="<?= ADMIN_STATIC_URL ?>js/jquery-1.8.3.min.js"></script>
<script src="<?= ADMIN_STATIC_URL ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= ADMIN_STATIC_URL ?>js/jquery.blockui.js"></script>
<script src="<?= ADMIN_STATIC_URL ?>js/js.js"></script>
<!-- ie8 fixes -->
<!--[if lt IE 9]>
<script src="js/excanvas.js"></script>
<script src="js/respond.js"></script>
<![endif]-->
<script type="text/javascript" src="<?= ADMIN_STATIC_URL ?>assets/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?= ADMIN_STATIC_URL ?>assets/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?= ADMIN_STATIC_URL ?>assets/uniform/jquery.uniform.min.js"></script>
<script src="<?= ADMIN_STATIC_URL ?>js/scripts.js"></script>
<script>
    jQuery(document).ready(function () {
        // initiate layout and plugins
        App.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
