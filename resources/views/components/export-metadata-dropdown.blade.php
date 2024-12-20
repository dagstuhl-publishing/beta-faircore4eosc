<div class="dropdown d-inline">
    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-database-fill-down"></i> {{ $text }}
    </button>
    <ul class="dropdown-menu">
        <li>
            <button class="dropdown-item" onclick="exportMetadata(event)" data-format="Codemeta-JSON" data-data="{{ $deposit->codemetaJson }}">
                Codemeta-JSON
            </button>
        </li>
        <li>
            <button class="dropdown-item" onclick="exportMetadata(event)" data-format="DataCite Record (JSON)" data-data="{{ $deposit->exportDataCiteRecord()->toApiJson() }}">
                DataCite Record (JSON)
            </button>
        </li>
        <li>
            <button class="dropdown-item" onclick="exportMetadata(event)" data-format="BibTeX Citation" data-data="{{ trim($deposit->exportBibEntry(false)->getBibTexString()) }}">
                BibTeX Citation
            </button>
        </li>
        <li>
            <button class="dropdown-item" onclick="exportMetadata(event)" data-format="BibLaTeX Citation" data-data="{{ trim($deposit->exportBibEntry(true)->getBibTexString()) }}">
                BibLaTeX Citation
            </button>
        </li>
    </ul>
</div>
