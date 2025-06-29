<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link rel="icon" type="image/jpg" href="{{ asset('new/src/assets/images/favicon (2).jpg') }}">
    <script defer src="{{ asset('new/src/js/main.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('new/src/css/output.css') }}" />
</head>

<body>
    <main class="relative">

        <div class="relative flex items-center justify-center w-full h-screen bg-gray16">
            <!-- Login Box -->

            <div class="p-10 bg-white xl:w-7/12 rounded-2xl">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_1px_1fr] gap-10 items-center">
                    <div class="mt-20 md:mt-0">
                        <div class="slider">
                            <div class="slides">
                                <div class="slide">
                                    <img src="{{ asset('new/src/assets/images/Express Delivery.png') }}"
                                        alt="Slide 1" />
                                    <h2 class="text-xl font-semibold text-black1">
                                        Welcome to Alshrouq Express
                                    </h2>
                                    <p class="mt-4 text-sm text-gray6">
                                        AlshrouqExpress Miles Ahead
                                        Ready to optimize your business logistics? Explore our smart solutions in Saudi
                                        Arabia, serving a diverse clientele, including thousands of restaurant chains,
                                        retailers, and e-commerce platforms, with a track record of nearly 20 million
                                        annual deliveries nationwide.
                                    </p>
                                </div>
                                <div class="slide">
                                    <img src="{{ asset('new/src/assets/images/Express Delivery.png') }}"
                                        alt="Slide 2" />
                                    <h2 class="text-xl font-semibold text-black1">
                                        Welcome to Alshrouq Express
                                    </h2>
                                    <p class="mt-4 text-sm text-gray6">
                                        AlshrouqExpress Miles Ahead
                                        Ready to optimize your business logistics? Explore our smart solutions in Saudi
                                        Arabia, serving a diverse clientele, including thousands of restaurant chains,
                                        retailers, and e-commerce platforms, with a track record of nearly 20 million
                                        annual deliveries nationwide.
                                    </p>
                                </div>
                                <div class="slide">
                                    <img src="{{ asset('new/src/assets/images/Express Delivery.png') }}"
                                        alt="Slide 3" />
                                    <h2 class="text-xl font-semibold text-black1">
                                        Welcome to Alshrouq Express
                                    </h2>
                                    <p class="mt-4 text-sm text-gray6">
                                        AlshrouqExpress Miles Ahead
                                        Ready to optimize your business logistics? Explore our smart solutions in Saudi
                                        Arabia, serving a diverse clientele, including thousands of restaurant chains,
                                        retailers, and e-commerce platforms, with a track record of nearly 20 million
                                        annual deliveries nationwide.
                                    </p>
                                </div>
                            </div>
                            <!-- Navigation Dots -->
                            <div class="nav-dots">
                                <span class="dot active" onclick="currentSlide(1)"></span>
                                <span class="dot" onclick="currentSlide(2)"></span>
                                <span class="dot" onclick="currentSlide(3)"></span>
                            </div>
                        </div>
                    </div>

                    <span class="w-[1px] h-3/4 bg-gray1"></span>
                    <!-- Form -->
                    <div class="flex flex-col">
                        <div class="flex items-center justify-center md:justify-start">
                            <img src="{{ asset('new/src/assets/images/logo-2.png') }}" alt=""
                                class="w-48') }}" />
                        </div>

                        <div class="flex flex-col gap-3 mt-10 md:mt-5">



                            <h3 class="text-xl font-semibold text-black1">Sign in</h3>
                            <p class="text-sm text-gray6">Fill your data to sign in</p>
                            @if (Session::has('success'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li style="color:red">{{ Session::get('success') }}</li>
                                    </ul>
                                </div>
                            @endif


                            <form class="flex flex-col gap-5 mt-8" action="{{ route('login') }}" method="post">
                                @csrf
                                <label
                                    class="flex items-center h-12 gap-3 p-2 border rounded-lg bg-gary16 border-gray1">
                                    <!-- Icon -->
                                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18 21.2642H6C3.582 21.2642 2.25 19.9322 2.25 17.5142V8.51419C2.25 6.09619 3.582 4.76419 6 4.76419H18C20.418 4.76419 21.75 6.09619 21.75 8.51419V17.5142C21.75 19.9322 20.418 21.2642 18 21.2642ZM6 6.26419C4.423 6.26419 3.75 6.93719 3.75 8.51419V17.5142C3.75 19.0912 4.423 19.7642 6 19.7642H18C19.577 19.7642 20.25 19.0912 20.25 17.5142V8.51419C20.25 6.93719 19.577 6.26419 18 6.26419H6ZM13.0291 13.6932L17.9409 10.1212C18.2759 9.87819 18.35 9.4082 18.106 9.0732C17.863 8.7392 17.3951 8.66319 17.0581 8.90819L12.146 12.4802C12.058 12.5442 11.941 12.5442 11.853 12.4802L6.94092 8.90819C6.60292 8.66319 6.13607 8.7402 5.89307 9.0732C5.64907 9.4082 5.72311 9.87719 6.05811 10.1212L10.97 13.6942C11.278 13.9182 11.639 14.0292 11.999 14.0292C12.359 14.0292 12.7221 13.9172 13.0291 13.6932Z"
                                            fill="#979DAB" />
                                    </svg>
                                    <!-- Input -->
                                    <input type="email" class="bg-transparent outline-none"
                                        placeholder="Email Address" name= "email" value="{{ old('email') }}" />
                                </label>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <label
                                    class="relative flex items-center h-12 gap-3 p-2 border rounded-lg bg-gary16 border-gray1">
                                    <!-- Icon -->
                                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M16.75 8.81815V7.51419C16.75 4.89519 14.619 2.76419 12 2.76419C9.381 2.76419 7.25 4.89519 7.25 7.51419V8.81815C5.312 9.07515 4.25 10.3602 4.25 12.5142V18.5142C4.25 20.9322 5.582 22.2642 8 22.2642H16C18.418 22.2642 19.75 20.9322 19.75 18.5142V12.5142C19.75 10.3612 18.688 9.07615 16.75 8.81815ZM12 4.26419C13.792 4.26419 15.25 5.72219 15.25 7.51419V8.76419H8.75V7.51419C8.75 5.72219 10.208 4.26419 12 4.26419ZM18.25 18.5142C18.25 20.0912 17.577 20.7642 16 20.7642H8C6.423 20.7642 5.75 20.0912 5.75 18.5142V12.5142C5.75 10.9372 6.423 10.2642 8 10.2642H16C17.577 10.2642 18.25 10.9372 18.25 12.5142V18.5142ZM13.27 14.5142C13.27 14.9262 13.058 15.2743 12.75 15.5013V17.5142C12.75 17.9282 12.414 18.2642 12 18.2642C11.586 18.2642 11.25 17.9282 11.25 17.5142V15.4761C10.962 15.2471 10.7649 14.9092 10.7649 14.5142C10.7649 13.8242 11.32 13.2642 12.01 13.2642H12.02C12.71 13.2642 13.27 13.8242 13.27 14.5142Z"
                                            fill="#979DAB" />
                                    </svg>

                                    <!-- Input -->
                                    <input type="password" class="bg-transparent outline-none" placeholder="Password"
                                        name="password" value="{{ env('LOGIN_PASS') }}" />

                                    <button type="button" class="absolute right-2">
                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21.229 14.3821C19.913 16.5781 16.96 20.2642 12 20.2642C11.037 20.2642 10.0861 20.1182 9.17408 19.8292C8.77908 19.7042 8.56105 19.2832 8.68605 18.8882C8.81005 18.4922 9.23496 18.2762 9.62696 18.3992C10.392 18.6412 11.19 18.7642 12 18.7642C16.222 18.7642 18.7901 15.5342 19.9441 13.6082C20.3521 12.9322 20.3521 12.0952 19.9461 11.4212C19.6001 10.8382 19.177 10.2422 18.72 9.69317C18.455 9.37417 18.4991 8.90126 18.8181 8.63726C19.1381 8.37226 19.61 8.41618 19.875 8.73418C20.381 9.34318 20.8509 10.0062 21.2329 10.6512C21.9259 11.7982 21.926 13.2301 21.229 14.3821ZM10.063 15.5123L3.53004 22.0452C3.38404 22.1912 3.19201 22.2652 3.00001 22.2652C2.80801 22.2652 2.61598 22.1922 2.46998 22.0452C2.17698 21.7522 2.17698 21.2772 2.46998 20.9842L5.64503 17.8091C4.31703 16.6641 3.35205 15.3512 2.76905 14.3802C2.07505 13.2302 2.07501 11.7983 2.77101 10.6473C4.08701 8.45127 7.04001 4.76519 12 4.76519C13.835 4.76519 15.565 5.28413 17.155 6.29913L20.469 2.98516C20.762 2.69216 21.237 2.69216 21.53 2.98516C21.823 3.27816 21.823 3.7532 21.53 4.0462L10.065 15.5113C10.065 15.5113 10.065 15.5123 10.064 15.5123C10.063 15.5123 10.063 15.5113 10.063 15.5123ZM9.6089 13.8453L13.3311 10.1231C12.9291 9.8961 12.478 9.76519 12 9.76519C10.484 9.76519 9.25099 10.9982 9.25099 12.5152C9.25099 12.9922 9.3829 13.4433 9.6089 13.8453ZM6.70704 16.7461L8.51905 14.9341C8.02405 14.2311 7.75099 13.3962 7.75099 12.5132C7.75099 10.1702 9.65701 8.26324 12 8.26324C12.884 8.26324 13.7179 8.5363 14.4209 9.0313L16.052 7.4002C14.787 6.6592 13.432 6.26226 12 6.26226C7.77801 6.26226 5.20992 9.49227 4.05592 11.4183C3.64792 12.0943 3.64797 12.9313 4.05397 13.6053C4.59097 14.5023 5.48304 15.7151 6.70704 16.7461ZM14.708 12.9493C14.528 14.1093 13.594 15.0442 12.436 15.2232C12.027 15.2862 11.7461 15.6692 11.8091 16.0782C11.8671 16.4492 12.1861 16.7142 12.5491 16.7142C12.5871 16.7142 12.6261 16.7111 12.6641 16.7051C14.4911 16.4231 15.908 15.0063 16.19 13.1783C16.253 12.7683 15.973 12.3863 15.563 12.3223C15.163 12.2613 14.771 12.5393 14.708 12.9493Z"
                                                fill="#404C66" />
                                        </svg>
                                    </button>
                                </label>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <p class="text-sm font-medium text-right text-black1">
                                    Forget Your Password?
                                </p>

                                <a href="/index.html" class="w-full">
                                    <button type="submit"
                                        class="w-full p-4 font-medium text-white rounded-lg bg-mainColor">
                                        Sign In
                                    </button>
                                </a>
                            </form>
                        </div>
                    </div>

                    <div class="flex items-center justify-center md:hidden">
                        <img src="{{ asset('new/src/assets/images/powerd-by.png') }}" alt="" />
                    </div>
                </div>
            </div>

            <div class="absolute hidden -translate-x-1/2 bottom-8 left-1/2 md:block">
                <img src="{{ asset('new/src/assets/images/powerd-by.png') }}" alt="" />
            </div>
        </div>
    </main>
</body>

</html>
