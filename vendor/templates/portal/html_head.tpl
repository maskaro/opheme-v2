{if $Data.loggedIn eq true}
	{foreach $Data.user.authorisation as $authModule}
		{if count($Data.user.authorisation[$authModule@key]) gt 0}
			{assign "smaOk" true scope="global"}
			{break}
		{/if}
	{/foreach}
	{if $Data.moduleName eq "discover" || $Data.moduleName eq "campaign"}
		{if isset($Data.user.allowance.discoversLeft) && $Data.user.allowance.discoversLeft lt 1}{assign "noDiscs" true scope="global"}{/if}
		{if isset($Data.user.allowance.campaignsLeft) && $Data.user.allowance.campaignsLeft lt 1}{assign "noCamps" true scope="global"}{/if}
	{/if}
	{if (isset($Data.user.allowance.accountTimeLeftSeconds) && $Data.user.allowance.accountTimeLeftSeconds eq 0)}{assign "trialEnded" true scope="global"}{/if}
{/if}
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{$Data.company.brand} | {$Data.title}</title>
		<link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="favicon/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="favicon/favicon-194x194.png" sizes="194x194">
		<link rel="icon" type="image/png" href="favicon/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="favicon/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="favicon/favicon-16x16.png" sizes="16x16">
		<meta name="msapplication-TileColor" content="#2d89ef">
		<meta name="msapplication-TileImage" content="/mstile-144x144.png">
		<meta name="theme-color" content="#ffffff">
		<link href='//fonts.googleapis.com/css?family=Lato:100,300,400,100italic,300italic,400italic' rel='stylesheet' type='text/css'>
		<!-- CSS -->
		{foreach $Data.cssFiles as $file}
			<link href="{$file}" rel="stylesheet" media="screen">
		{/foreach}
		{foreach $Data.cssExtra as $code}
			{if strlen($code) > 0}
				<style rel="stylesheet" media="screen">{$code}</style>
			{/if}
		{/foreach}
		{if strlen({$Data.cssCompanyChanges}) > 0}
			<style rel="stylesheet" media="screen">{$Data.cssCompanyChanges}</style>
		{/if}
		{if isset($Data.cssModuleFile)}
			<link href="{$Data.cssModuleFile}" rel="stylesheet" media="screen">
		{/if}
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script>
			moduleName = "{$Data.title}";
			moduleId = "{$Data.moduleName}";
			brand = "{$Data.company.brand}";
			{if isset($smaOk) && $Data.user.account.allSet eq true}
				accountOK = true;
				_oUserId = "{$Data.user.account.id}";
				localTimeOffSet = (new Date()).getTimezoneOffset() * 60;
				lastActionTime = {$smarty.session.user.previous_interaction_check};// + localTimeOffSet;
			{else}
				accountOK = false;
			{/if}
		</script>
		{foreach $Data.jsFiles as $file}
			<script src="{$file}"></script>
		{/foreach}
		{foreach $Data.jsFilesExtraTop as $file}
			<script src="{$file}"></script>
		{/foreach}
		{if isset($smaOk) && $Data.user.account.allSet eq true && $Data.moduleName neq "interaction"}
			<script>
				$(function() {
					getInteractionUpdateCount();
					setInterval(function() { getInteractionUpdateCount(); }, 30000);
				});
			</script>
		{/if}
		<script>
			{literal}(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-38710014-2', 'opheme.com');ga('send', 'pageview');{/literal}
		</script>
	</head>
	<body>