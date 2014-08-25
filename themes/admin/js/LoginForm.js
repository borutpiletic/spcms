/**
 * Login form controller
 */
function LoginForm($scope, $http, $location) 
{
	$scope.messages = [];
	
	$scope.user = {
		email:'',
		password:''
	};
	
	/**
	 * Login action - user submit the form
	 */
	$scope.login = function()
	{
		// Run server-side authentication		
		authenticate($scope.user);
	}
	
	authenticate = function(user)
	{
		$http.post('auth', user).error(function(resp)
		{
			console.log(resp.user.name);
			
		}).success(function(resp)
		{
			
		});
	}
}

