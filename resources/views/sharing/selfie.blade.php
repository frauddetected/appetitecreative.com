<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $project->name }} | Selfie</title>
    <meta name="description" content="{{ $project->name }} Connected Experiences">
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $project->name }}" />
    <meta property="og:description" content="{{ $project->name }} Connected Experiences" />
    <meta property="og:url" content="{{ Request::fullUrl() }}" />
    <meta property="og:image" content="https://cdn.shortpixel.ai/client/w_300,h_300,c_center,q_lossy,ret_wait/https://share.appetite.link/media/selfies/{{ $selfie }}.png" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

    <style>
    body{ 
        background: {{ $project->sharing->style->primary_color }};
        color: {{ $project->sharing->style->secondary_color }};
    }

    .bg-primary{
        background: {{ $project->sharing->style->primary_color }}
    }

    .bg-secondary{
        background: {{ $project->sharing->style->secondary_color }}
    }

    .text-primary{
        color: {{ $project->sharing->style->primary_color }}
    }

    .text-secondary{
        color: {{ $project->sharing->style->secondary_color }}
    }
    </style>

    <div class="text-center min-h-screen flex flex-col items-center justify-center">
        
        <img src="https://share.appetite.link/media/selfies/{{ $selfie }}.png" class="w-9/12 xl:w-3/12" style="border: 10px solid {{ $project->sharing->style->secondary_color }}" alt="">
        <a href="{{ $project->domain }}" class="bg-secondary text-primary rounded-sm py-2 px-4 mt-4">Visit {{ $project->name }}</a>

    </div>

</body>
</html>