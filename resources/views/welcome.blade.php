<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Task Manager</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

</head>
<body>
<div id="app">
    <v-app class="bg-blue-grey-lighten-5">

        <v-container class="pt-5">

            <projects-component></projects-component>

        </v-container>

    </v-app>

</div>

<script src="{{@asset('js/app.js')}}"></script>
</body>
</html>

