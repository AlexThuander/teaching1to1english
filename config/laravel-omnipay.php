<?php

return [

	// The default gateway to use
	'default' => 'paypal',

	// Add in each gateway here
	'gateways' => [
		'paypal' => [
			'driver'  => 'PayPal_Express',
			'options' => [
            'username'  => 'thuander234_api1.gmail.com',
            'password'  => 'M6EXTNM8HP99EPHV',
            'signature' => 'AcMHY2f-Kd21ynFvPy9lMZ1S5g2FALtKGL1yRHpdAKEQkV-ADHqObqnL',
            'solutionType' => '',
            'landingPage'    => '',
            'headerImageUrl' => '',
            'brandName' =>  'Your app name',
            'testMode' => true
			]
		]
	]

];