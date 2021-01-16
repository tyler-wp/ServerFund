	<?php if ($_SESSION['panel'] === "N/A") : ?>
	<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-expand-md navbar-inverse" role="navigation">
	<?php else : ?>
	<nav class="site-navbar nb-default navbar navbar-default navbar-fixed-top navbar-mega navbar-expand-md navbar-inverse" role="navigation">
		<?php endif; ?>
		<div class="navbar-header">
			<?php if($_SESSION['panel'] === "N/A"): ?>
			<button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided" data-toggle="menubar">
				<span class="sr-only">Toggle navigation</span>
				<span class="hamburger-bar"></span>
			</button>
			<button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse" data-toggle="collapse">
				<i class="icon wb-more-horizontal" aria-hidden="true"></i>
			</button>
			<?php endif; ?>
			<a class="navbar-brand navbar-brand-center site-gridmenu-toggle" href="<?php echo $host_url; ?>/index">
				<!-- <img class="navbar-brand-logo" src="assets/images/logo.png" title="Remark"> -->
				<i class="site-menu-icon wb-tag" aria-hidden="true"></i> <span class="navbar-brand-text hidden-xs-down"> ServerFund</span>
			</a>
		</div>

		<div class="navbar-container container-fluid">
			<!-- Navbar Collapse -->
			<div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
				<?php if($_SESSION['panel'] === "N/A"): ?>
				<!-- Navbar Toolbar -->
				<ul class="nav navbar-toolbar">
					<li class="nav-item hidden-float" id="toggleMenubar">
						<a class="nav-link" data-toggle="menubar" href="#" role="button">
							<i class="icon hamburger hamburger-arrow-left">
								<span class="sr-only">Toggle menubar</span>
								<span class="hamburger-bar"></span>
							</i>
						</a>
					</li>
				</ul>
				<!-- End Navbar Toolbar -->
				<?php endif; ?>

				<!-- Navbar Toolbar Right -->
				<ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
					<li class="nav-item dropdown" style="margin-right: 15px; margin-top: 5px;">
						<a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
							<?php if (loggedIn) : ?>
								<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
							<?php else : ?>
								Guest
							<?php endif; ?>
						</a>
						<div class="dropdown-menu" role="menu">
							<?php if (loggedIn) : ?>
								<a class="dropdown-item" href="<?php echo $host_url; ?>/user/billing" role="menuitem"><i class="icon wb-payment" aria-hidden="true"></i> Billing</a>
								<a class="dropdown-item" href="<?php echo $host_url; ?>/user/settings" role="menuitem"><i class="icon wb-settings" aria-hidden="true"></i> Settings</a>
								<div class="dropdown-divider" role="presentation"></div>
								<a class="dropdown-item" href="<?php echo $host_url; ?>/logout" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Logout</a>
							<?php else : ?>
								<a class="dropdown-item" href="<?php echo $host_url; ?>/login" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Login</a>
							<?php endif; ?>
						</div>
					</li>
				</ul>
				<!-- End Navbar Toolbar Right -->
			</div>
			<!-- End Navbar Collapse -->
		</div>
	</nav>
	<?php if($_SESSION['panel'] === "N/A"): ?>
	<div class="site-menubar">
		<div class="site-menubar-body">
			<div>
				<div>
					<?php if ($_SESSION['panel'] === "N/A") : ?>
						<ul class="site-menu" data-plugin="menu">
							<li class="site-menu-category">Navigation</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/index">
									<i class="site-menu-icon wb-home" aria-hidden="true"></i>
									<span class="site-menu-title">Home</span>
								</a>
							</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/user/panels">
									<i class="site-menu-icon wb-menu" aria-hidden="true"></i>
									<span class="site-menu-title">My Stores</span>
								</a>
							</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/user/membership">
									<i class="site-menu-icon wb-payment" aria-hidden="true"></i>
									<span class="site-menu-title">Membership</span>
								</a>
							</li>
							<?php
							$sql = "SELECT count(*) FROM `server_issues`";
							$result = $pdo->prepare($sql);
							$result->execute();
							$number_of_rows = $result->fetchColumn();
							if ($number_of_rows !== 0) : ?>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/user/support">
									<i class="site-menu-icon wb-help" aria-hidden="true"></i>
									<span class="site-menu-title">Support</span>
									<div class="site-menu-badge">
                                        <span class="badge badge-pill badge-danger"><?php echo $number_of_rows; ?></span>
                                    </div>
								</a>
							</li>
							<?php else : ?>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/user/support">
									<i class="site-menu-icon wb-help" aria-hidden="true"></i>
									<span class="site-menu-title">Support</span>
								</a>
							</li>
							<?php endif; ?>

							<?php if(is_moderator === "true" || is_admin === "true" || is_superAdmin === "true"): ?>
							<li class="site-menu-category">Administration</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/admin/users">
									<i class="site-menu-icon wb-users" aria-hidden="true"></i>
									<span class="site-menu-title">Users</span>
								</a>
							</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/admin/#">
									<i class="site-menu-icon wb-briefcase" aria-hidden="true"></i>
									<span class="site-menu-title">Orders</span>
								</a>
							</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/admin/#">
									<i class="site-menu-icon wb-layout" aria-hidden="true"></i>
									<span class="site-menu-title">Stores</span>
								</a>
							</li>
							<?php endif; ?>
						</ul>
					<?php else : ?>
						<!-- <ul class="site-menu" data-plugin="menu">
							<li class="site-menu-category">Navigation</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>">
									<i class="site-menu-icon wb-home" aria-hidden="true"></i>
									<span class="site-menu-title">Home</span>
								</a>
							</li>
							<li class="site-menu-item has-sub">
								<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>/store">
									<i class="site-menu-icon wb-shopping-cart" aria-hidden="true"></i>
									<span class="site-menu-title">Packages</span>
								</a>
							</li>
							<?php if ($_SESSION['panel_owner'] === $_SESSION['user_id']) : ?>
								<li class="site-menu-category">Admin</li>
								<li class="site-menu-item has-sub">
									<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>/admin/settings">
										<i class="site-menu-icon wb-hammer" aria-hidden="true"></i>
										<span class="site-menu-title">Settings</span>
									</a>
								</li>
								<li class="site-menu-item has-sub">
									<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>/admin/packages">
										<i class="site-menu-icon wb-tag" aria-hidden="true"></i>
										<span class="site-menu-title">Packages</span>
									</a>
								</li>
								<li class="site-menu-item has-sub">
									<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>/admin/orders">
										<i class="site-menu-icon wb-order" aria-hidden="true"></i>
										<span class="site-menu-title">Orders</span>
									</a>
								</li>
								<li class="site-menu-item has-sub">
									<a href="<?php echo $host_url; ?>/p/<?php echo $_SESSION['panel_abrv']; ?>/admin/page-editor">
										<i class="site-menu-icon wb-edit" aria-hidden="true"></i>
										<span class="site-menu-title">Page Editor</span>
									</a>
								</li>
							<?php endif; ?>
						</ul> -->
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>