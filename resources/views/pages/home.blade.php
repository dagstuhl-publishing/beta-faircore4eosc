@extends("layouts.app")

@section("content")
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="m-0">FAIRCORE4EOSC RSAC Demo</h2>
        <a href="https://faircore4eosc.eu/eosc-core-components/eosc-research-software-apis-and-connectors-rsac">
            <img style="height: 4em" src="{{ asset("images/rsac.svg") }}">
        </a>
    </div>
    <p>
        This project serves as a demo for some software components developed by
        <a href="https://www.dagstuhl.de/en/publishing">Dagstuhl Publishing</a>
        as part of the
        <a href="https://faircore4eosc.eu">FAIRCORE4EOSC project</a>
        in the context of the
        <a href="https://faircore4eosc.eu/eosc-core-components/eosc-research-software-apis-and-connectors-rsac">EOSC Research Software APIs and Connectors (RSAC)</a>
        component.
    </p>
    <p>
        The following key components form the basis of this demo server:
    </p>
    <ul>
        <li>two PHP-based API-clients
            (<a href="https://github.com/dagstuhl-publishing/swh-archive-client">swh-archive-client</a>,
            <a href="https://github.com/dagstuhl-publishing/swh-archive-client">swh-deposit-client</a>)
            for <a href="https://www.softwareheritage.org/">SoftwareHeritage</a>
            and
        </li>
        <li>a form for providing (core) metadata in <a href="https://codemeta.github.io/">CodeMeta</a> standard.</li>
    </ul>
    <p>
        Some of these components have already been integrated into the Dagstuhl Publishing production systems
        (<a href="https://submission.dagstuhl.de">Dagstuhl Submission Server</a>, <a href="https://drops.dagstuhl.de">Publication Server DROPS</a>)
        for six months. A full integration is in progress.
    </p>
    <p>
        After logging in to this demo server, you can:
    </p>
    <ul>
        <li>archive existing repositories (e.g., from platforms like github or gitlab) at SoftwareHeritage,</li>
        <li>upload a software-project as a zip-file to SoftwareHeritage, and</li>
        <li>provide metadata in form of CodeMeta-JSON, either by uploading a file or by inputting the data in an interactive form.</li>
    </ul>
    <p>
        In this way, the server shows in a clear setting what is difficult to demonstrate in Dagstuhl's productive system,
        since there it applies to different users in different roles (author, editorial staff)
        and in different phases of the publication process (submission, approval).
    </p>

    <x-demo-note />

    <p>
        This project was developed by <a href="https://www.dagstuhl.de/en/publishing">Dagstuhl's Publishing Team</a>
        in PHP using the Laravel framework.
        The source code is available <a href="https://github.com/dagstuhl-publishing/beta-faircore4eosc">on GitHub</a>.
    </p>
@endsection
