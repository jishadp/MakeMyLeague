<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-lg bg-white p-10 rounded-xl shadow-xl">
        <h2 class="text-3xl font-bold text-center mb-8">Login</h2>

        <!-- Status Message -->
        <div id="status" class="mb-6 hidden text-green-600 text-base font-medium text-center">
            Login successful!
        </div>

        <form method="POST" action="{{ route('do.login')}}">
            @csrf
            <!-- Mobile -->
            <div class="mb-6">
                <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                <input id="mobile" type="tel" name="mobile" value="9876567876"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                       required autofocus autocomplete="tel">
                <p class="mt-2 text-sm text-red-600 hidden" id="mobile-error">Invalid mobile</p>
            </div>

            <!-- PIN -->
            <div class="mb-6">
                <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN</label>
                <input id="pin" type="password" name="pin" value="4334"
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                       required autocomplete="current-password">
                <p class="mt-2 text-sm text-red-600 hidden" id="pin-error">PIN required</p>
            </div>

            <!-- Remember Me -->
            <div class="mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <!-- Forgot password & Login button -->
            <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
                <a href="#" class="underline text-sm text-gray-600 hover:text-gray-900 mb-4 sm:mb-0">
                    Forgot your password?
                </a>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Log in
                </button>
            </div>

            <!-- Demo Account Info -->
            <div class="p-6 bg-blue-50 rounded-lg">
                <h3 class="text-base font-medium text-blue-800 mb-3">Demo Account:</h3>
                <p class="text-sm text-blue-700">Email: manager@cricket.com</p>
                <p class="text-sm text-blue-700">Password: password123</p>
            </div>
        </form>
    </div>
</body>
</html> 