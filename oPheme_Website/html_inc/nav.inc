		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<!-- Be sure to leave the brand out there if you want it shown -->
					<a class="brand" href="/index">o<span class="orange">:P</span>heme</a>
					<!-- Everything you want hidden at 940px or less, place within here -->
					<div class="nav-collapse collapse">
						<ul class="nav pull-right">
							<li<?php if (stripos($_SERVER['PHP_SELF'], 'index') !== false) echo ' class="active"'; ?>><a href="/index">All About oPheme</a></li>
							<li class="divider-vertical"></li>
							<li><a href="https://portal.opheme.com/login?register" target="_blank">Free Trial</a></li>
							<li class="divider-vertical"></li>
							<li<?php if (stripos($_SERVER['PHP_SELF'], 'support') !== false) echo ' class="active"'; ?>><a href="/support">Support</a></li>
							<li class="divider-vertical"></li>
							<li<?php if (stripos($_SERVER['PHP_SELF'], 'contact') !== false) echo ' class="active"'; ?>><a href="/contact">Get in Touch</a></li>
							<li class="divider-vertical"></li>
							<li><a href="https://portal.opheme.com/login" target="_blank">Login</a></li>
						</ul>
						<!-- .nav, .navbar-search, .navbar-form, etc -->
					</div>
				</div>
			</div>
		</div>