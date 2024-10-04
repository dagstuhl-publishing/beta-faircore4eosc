@extends("layouts.app")

@section("content")
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="m-0">FAIRCORE4EOSC RSAC Demo</h2>
        <a href="https://faircore4eosc.eu/eosc-core-components/eosc-research-software-apis-and-connectors-rsac">
            <img style="height: 4em" src="{{ asset("images/rsac.svg") }}">
        </a>
    </div>
    <p>
        This project serves as a demo for the
        <a href="https://faircore4eosc.eu/eosc-core-components/eosc-research-software-apis-and-connectors-rsac">EOSC Research Software APIs and Connectors (RSAC) components.</a>
    </p>
    <p>
        In this project, we implemented PHP-based API clients for SoftwareHeritage.
        After logging in, you can:
    </p>
    <ul>
        <li>archive existing repositories (e.g. on GitHub),</li>
        <li>upload a project as a zip-file, and</li>
        <li>provide metadata in form of Codemeta-JSON, either by uploading a file or by inputting the data in an interactive form.</li>
    </ul>
    This project was developed by <a href="https://www.dagstuhl.de/en/publishing">Dagstuhl's Publishing Team</a>
    in PHP using the Laravel framework.
    The source code is available <a href="https://github.com/dagstuhl-publishing/beta-faircore4eosc">on GitHub</a>.
@endsection
