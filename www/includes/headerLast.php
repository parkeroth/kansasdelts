</head>
<body>

<div id="container">
	<div id="header">
		<div id="headerimg">
         	<img src="/img/header.jpg"  alt="" />
          <div id="headerNavBarLeft">
            	<?php echo strtolower(date("l, F j, Y"));?>
            </div>
         	<div id="headerNavBarRight">
               	<a href="/account.php">my account</a> | 
                <?php if(isset($session->member_id)){ ?>
			<a href="/logout.php">logout</a> 
                <?php } else { ?>
			<!-- <a href="/loginForm.php">login</a>  -->
                        <div id="login">
                                <a href="#">login</a>
                                <form action="/loginForm.php" method="post">
                                    <div>
                                            <input name="username" id="username" type="text" />
                                            <label for="username" class="loginLabel">Username</label>
                                    </div>
                                    <div>
                                            <input name="password" id="password" type="password"/>
                                            <label for="password" class="loginLabel">Password</label>
                                    </div>
                                    <div>
                                            <label for="rememberMe" style="display: inline-block;">Remember Me:</label>
                                            <input name="remember" id="rememberMe" type="checkbox" style="display: inline-block;" />
                                    </div>
                                    <div>
                                            <input type="submit" value="Log in!" style="text-align: center; margin-right: auto; margin-left: auto;" />
                                    </div>
                                </form>
                        </div> <!-- End login div -->
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="beef">
		<div id="dropdown-container">
			<ul id="topMenu" class="dropdown">
                <li class="top"><a href="/index.php">Home</a></li>
                <li class="top"><a href="#">About Us</a>
					<ul>
						<li><a href="/meetTheGuys.php">Meet The Guys</a></li>
						<li><a href="/who.php">Who We Are</a></li>
						<li><a href="/creed.php">Delt Creed</a></li>
						<!-- <li><a href="/gammatau.php">Gamma Tau</a></li> -->
						<li><a href="/contactUs.php">Contact Us</a></li>
						<li><a href="http://delts.org" target="_blank">National Site</a></li>
					</ul>
                </li>
				<li class="top"><a href="http://kansasdelts.tumblr.com" target="_blank">News</a></li>
				<li class="top"><a href="#">Photos</a>
					<ul>
						<!--<li><a href="housetour.html">House Tour</a></li>-->
						<li class="sub"><a href="/photos/social.php">Social</a></li>
						<li><a href="/photos/service.php">Service</a></li>
						<li><a href="/photos/alumni.php">Alumni</a></li>
					</ul>
				</li>
                <li class="top"><a href="#">Scholarship</a>
					<ul>
						<li><a href="/ryanFamilySummary.php">Ryan Family</a></li>
						<li><a href="/scholarship.php">Rush Scholarship</a></li>
					</ul>
                </li>
                <li class="top"><a href="#">Alumni</a>
                	<ul>
						<li><a href="/alumni.php">Alumni Home</a></li>
                   		<li><a href="/recruitment/forms/submitAlumni.php">Refer a Man</a></li>
                        <li><a href="/contactUs.php">Contact Chapter</a></li>
                	</ul>
                </li>
                <li class="top"><a href="/rushdelt.php">Rush Delt</a>
                	<ul>
                   		<li><a href="/unique.php">Why Delt?</a></li>
                    	<li><a href="/heritage.php">Heritage</a></li>
                    	<li><a href="/parents.php">Parents</a></li>
                        <li><a href="/values.php">Values</a></li>
                        <li><a href="/scholarship.php">Scholarship</a></li>
                        <li><a href="/recruitment/forms/submitSelf.php">Rush Form</a></li>
                	</ul>
                </li>
            </ul>
        </div> <!-- end menuh div -->	
