<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>

        <!-- Status Message -->
        <div id="status" class="mb-4 hidden text-green-600 text-sm">
            Login successful!
        </div>

        <form method="POST" action="{{ route('do.login')}}">
            <!-- Email Address -->
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Mobile</label>
                <input id="email" type="text" name="mobile" value="9876567876"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       required autofocus autocomplete="username">
                <p class="mt-2 text-sm text-red-600 hidden" id="email-error">Invalid email</p>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">PIN</label>
                <input id="password" type="password" name="pin" value="4334"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       required autocomplete="current-password">
                <p class="mt-2 text-sm text-red-600 hidden" id="password-error">Password required</p>
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <!-- Forgot password & Login button -->
            <div class="flex items-center justify-between mt-4">
                <a href="#" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Forgot your password?
                </a>
                <button type="submit"
                        class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Log in
                </button>
            </div>

            <!-- Demo Account Info -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Demo Account:</h3>
                <p class="text-sm text-blue-700">Email: manager@cricket.com</p>
                <p class="text-sm text-blue-700">Password: password123</p>
            </div>
        </form>
    </div>
</body>
</html>
