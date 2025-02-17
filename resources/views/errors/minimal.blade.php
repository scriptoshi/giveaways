<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>
    @vite('resources/js/app.js')
</head>

<body class="antialiased ">
    <section class="bg-white  h-screen dark:bg-gray-900">
        <div class="py-8 px-4 flex flex-col h-full  items-center mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto my-auto max-w-screen-sm text-center">
                <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-emerald-600 dark:text-emerald-500"> @yield('code')</h1>
                <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">@yield('title')</p>
                <p class="mb-4 text-lg font-semibold text-gray-500 dark:text-gray-400">@yield('message')</p>
                <a href="/" class="inline-flex text-white bg-emerald-600 hover:bg-emerald-800 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-emerald-900 my-4">Back to Homepage</a>
            </div>
        </div>
    </section>
</body>

</html>