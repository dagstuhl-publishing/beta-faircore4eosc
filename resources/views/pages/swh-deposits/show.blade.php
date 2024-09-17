@extends("layouts.app")

@section("content")
    <h2 class="mb-5">Deposit <code>{{ $deposit->uuid }}</code></h2>

    <div class="mb-5 text-center">
        <p class="mb-3">
            @if($deposit->archiveFilename !== null)
                <b>Archive:</b>
                <i class="bi bi-file-earmark-zip-fill"></i>
                {{ $deposit->archiveFilename }} ({{ $deposit->getFormattedArchiveSize() }})
            @else
                <b>Origin:</b>
                {{ $deposit->originSwhId }}
            @endif
        </p>

        <p class="mb-3">
            <b>Uploaded:</b>
            {{ $deposit->created_at->diffForHumans() }} ({{ $deposit->created_at }})
        </p>

        <p class="mb-3">
            <b>Status:</b>
            <span title="{{ $deposit->depositStatus ?? "pending" }}">
                @switch($deposit->depositStatus)
                    @case("partial")
                    @case("deposited")
                    @case("verified")
                    @case("loading")
                        <i class="bi bi-three-dots"></i>
                        Deposited - Uploaded to SoftwareHeritage
                        @break
                    @case("rejected")
                        <i class="bi bi-exclamation-circle-fill"></i>
                        Rejected - Rejected by SoftwareHeritage
                        @break
                    @case("done")
                        <i class="bi bi-check-circle-fill"></i>
                        Done - Fully archived by SoftwareHeritage
                        @break
                    @case("failed")
                        <i class="bi bi-exclamation-circle-fill"></i>
                        Failed - An error occured on SoftwareHeritage
                        @break
                    @default
                        <i class="bi bi-three-dots"></i>
                        Pending - Not yet deposited to SoftwareHeritage
                        @break
                @endswitch
            </span>
        </p>

        <p class="mb-0">
            @if($deposit->archivePath !== null)
                <a class="btn btn-primary" href="{{ asset("storage/deposits/{$deposit->archivePath}") }}" download="{{ $deposit->archiveFilename }}">
                    <i class="bi bi-download"></i> Download
                </a>
            @endif

            @if($deposit->depositSwhId !== null)
                <a class="btn btn-primary @if($deposit->depositSwhIdContext === null) disabled @endif" href="{{ $deposit->getBrowseUrl() }}">
                    <i class="bi bi-box-arrow-up-right"></i> Browse Archive
                </a>
            @endif

            <a class="btn btn-primary" href="#">
                <i class="bi bi-database-fill-down"></i> Export Metadata
            </a>

            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#statusDetailsModal"
                @if($deposit->depositId === null) disabled @endif
            >
                <i class="bi bi-activity"></i> Show Status Details
            </button>
        </p>
    </div>

    @if($deposit->depositId !== null)
        <div class="modal fade" id="statusDetailsModal" tabindex="-1" aria-labelledby="statusDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="statusDetailsModalLabel">Status Details</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-nowrap"><b>Deposit Id:</b></td>
                                    <td class="w-100">{{ $deposit->depositId }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Deposit Status:</b></td>
                                    <td class="w-100">{{ $depositStatus->value }} - {{ $depositStatus->getDescription() }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Deposit Status Detail:</b></td>
                                    <td class="w-100">{{ $depositResponse->getDepositStatusDetail() ?? "-" }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Deposit SwhId:</b></td>
                                    <td class="w-100">{{ $depositResponse->getDepositSwhId() ?? "-" }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Deposit SwhId Context:</b></td>
                                    <td class="w-100">{{ $depositResponse->getDepositSwhIdContext() ?? "-" }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Deposited at:</b></td>
                                    <td class="w-100">{{ $deposit->deposited_at }}</td>
                                </tr>
                                <tr>
                                    <td class="text-nowrap"><b>Finished at:</b></td>
                                    <td class="w-100">{{ $deposit->finished_at ?? "-" }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p><b>Latest Response:</b> (status {{ $deposit->latestResponseStatus }})</p>
                        <pre class="overflow-auto"><code>{{ $deposit->latestResponseBody }}</code></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <h3>Metadata</h3>
    <pre><code>{{ json_encode(json_decode($deposit->codemetaJson), JSON_PRETTY_PRINT) }}</code></pre>
@endsection
