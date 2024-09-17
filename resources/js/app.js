import "./bootstrap";

import { createApp } from "vue/dist/vue.esm-bundler";

function newAuthor() {
    return {
        "@type": "Person",
        "@id": "",
        givenName: "",
        familyName: "",
        email: "",
        affiliation: {
            "@type": "Organization",
            "name": "",
        },
    };
}

function newCodemetaJson() {
    return {
        "@context": "https:\/\/doi.org\/10.5063\/schema\/codemeta-2.0",
        "@type": "SoftwareSourceCode",
        identifier: "", //TODO
        name: "",
        author: [ newAuthor() ],
        description: "",
        version: "",
        dateCreated: "",
        datePublished: "",
        license: [ "" ],
        keywords: [ "" ],
        programmingLanguage: [ "" ],
        developmentStatus: "",
    };
}

function cleanUpCodemetaJson(obj, inner) {
    if(Array.isArray(obj)) {
        obj = obj
            .map((v) => cleanUpCodemetaJson(v, true))
            .filter((v) => v !== null);
        if(obj.length === 0) {
            return null;
        } else if(obj.length === 1) {
            return obj[0];
        } else {
            return obj;
        }
    }

    if(typeof obj === "string") {
        obj = obj.trim();
        if(obj === "") {
            return null;
        } else {
            return obj;
        }
    }

    let res = {};
    let nonempty = !inner;

    Object.entries(obj).forEach(([key, value]) => {
        if(key.startsWith("@")) {
            res[key] = value;
            return;
        }

        value = cleanUpCodemetaJson(value, true);
        if(value === null) {
            return;
        }

        res[key] = value;
        nonempty = true;
    });

    return nonempty ? res : null;
}

const SwhDepositForm = {
    props: [
        "licenses",
        "languages",
        "initialSwhId",
    ],

    data() {
        if(this.initialSwhId !== null) {
            return {
                type: "metadata",
                swhId: this.initialSwhId,
                codemetaJson: newCodemetaJson(),
            };
        } else {
            return {
                type: "archive",
                swhId: "",
                codemetaJson: newCodemetaJson(),
            };
        }
    },

    methods: {
        addAuthor() {
            this.codemetaJson.author.push(newAuthor());
        },

        removeAuthor(index) {
            this.codemetaJson.author.splice(index, 1);
        },

        addLicense() {
            this.codemetaJson.license.push("");
        },

        removeLicense(index) {
            this.codemetaJson.license.splice(index, 1);
        },

        addKeyword() {
            this.codemetaJson.keywords.push("");
        },

        removeKeyword(index) {
            this.codemetaJson.keywords.splice(index, 1);
        },

        addProgrammingLanguage() {
            this.codemetaJson.programmingLanguage.push("");
        },

        removeProgrammingLanguage(index) {
            this.codemetaJson.programmingLanguage.splice(index, 1);
        },
    },

    computed: {
        outputJson() {
            return cleanUpCodemetaJson(this.codemetaJson);
        },
    },

    template: `
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" type="button" id="archive-tab"
                        :class="{ active: type === 'archive' }"
                        @click="type = 'archive'"
                        role="tab" aria-controls="archive-tab-pane" aria-selected="true"
                    >
                        Upload Archive
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" type="button" id="metadata-tab"
                        :class="{ active: type === 'metadata' }"
                        @click="type = 'metadata'"
                        role="tab" aria-controls="metadata-tab-pane" aria-selected="false"
                    >
                        Metadata Deposit
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3 mb-3 border border-top-0">
                <div v-if="type === 'archive'" role="tabpanel" aria-labelledby="archive-tab" tabindex="0">
                    <input type="hidden" name="type" value="archive">
                    <p>Upload a .zip or .tar-archive.</p>
                    <input class="form-control" type="file" name="archive">
                </div>
                <div v-if="type === 'metadata'" role="tabpanel" aria-labelledby="metadata-tab" tabindex="0">
                    <input type="hidden" name="type" value="metadata">
                    <p>Create a metadata deposit for an already archived object.</p>
                    <input class="form-control" type="text" name="originSwhId" placeholder="SwhId" v-model="swhId">
                </div>
            </div>

            <h3 class="mb-3">Metadata</h3>

            <div class="mb-3 p-3 border">
                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-name">
                        Name:
                    </label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" placeholder="Project title / Software title / ..."
                            id="metadata-form-name" v-model="codemetaJson.name">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">
                        Authors:
                    </label>
                    <div class="col-md-9">
                        <div v-for="(author, i) in codemetaJson.author" class="mb-3">
                            <div class="p-3 border">
                                <p class="mb-3 d-flex align-items-center justify-content-between">
                                    <b>Author {{ i + 1 }}:</b>
                                    <button class="btn btn-sm btn-danger" type="button"
                                        :disabled="codemetaJson.author.length <= 1" @click="removeAuthor(i)"
                                    >
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </p>
                                <div class="row mb-3">
                                    <label class="col-md-2 col-form-label" :for="'metadata-form-author-' + i + '-given-name'">
                                        Given Name:
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" placeholder="John"
                                            :id="'metadata-form-author-' + i + '-given-name'" v-model="author.givenName">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-2 col-form-label" :for="'metadata-form-author-' + i + '-family-name'">
                                        Family Name:
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" placeholder="Smith"
                                            :id="'metadata-form-author-' + i + '-family-name'" v-model="author.familyName">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-2 col-form-label" :for="'metadata-form-author-' + i + '-email'">
                                        Email:
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" placeholder="john.smith@example.com"
                                            :id="'metadata-form-author-' + i + '-email'" v-model="author.email">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-2 col-form-label" :for="'metadata-form-author-' + i + '-id'">
                                        Identifier (URI):
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" placeholder="ORCID, etc."
                                            :id="'metadata-form-author-' + i + '-id'" v-model="author['@id']">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-2 col-form-label" :for="'metadata-form-author-' + i + '-affiliation'">
                                        Affiliation:
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" placeholder="Department, University, Institute, ..."
                                            :id="'metadata-form-author-' + i + '-affiliation'" v-model="author.affiliation.name">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-primary" type="button" @click="addAuthor()">
                                <i class="bi bi-plus-lg"></i> Add Author
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-description">
                        Description:
                    </label>
                    <div class="col-md-9">
                        <textarea class="form-control" rows="5" placeholder="Short description of your software"
                            id="metadata-form-description" v-model="codemetaJson.description"
                        ></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-version">
                        Version:
                    </label>
                    <div class="col-md-9">
                        <input class="form-control" type="text" placeholder="Version number, e.g. 1.2.3"
                            id="metadata-form-version" v-model="codemetaJson.version">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-date-created">
                        Creation Date:
                    </label>
                    <div class="col-md-9">
                        <input class="form-control" type="date"
                            id="metadata-form-date-created" v-model="codemetaJson.dateCreated">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-date-published">
                        Publication Date:
                    </label>
                    <div class="col-md-9">
                        <input class="form-control" type="date"
                            id="metadata-form-date-published" v-model="codemetaJson.datePublished">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">
                        Licenses:
                    </label>
                    <div class="col-md-9">
                        <div v-for="(license, i) in codemetaJson.license" class="input-group mb-3">
                            <input class="form-control" list="licenses" v-model="codemetaJson.license[i]">
                            <button class="btn btn-outline-danger" type="button"
                                :disabled="codemetaJson.license.length <= 1" @click="removeLicense(i)"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-primary" type="button" @click="addLicense()">
                                <i class="bi bi-plus-lg"></i> Add License
                            </button>
                        </div>
                    </div>
                </div>

                <datalist id="licenses">
                    <option v-for="(title, id) in licenses" :value="id" :label="id + ': ' + title"></option>
                </datalist>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">
                        Keywords:
                    </label>
                    <div class="col-md-9">
                        <div v-for="(keyword, i) in codemetaJson.keywords" class="input-group mb-3">
                            <input class="form-control" type="text" v-model="codemetaJson.keywords[i]">
                            <button class="btn btn-outline-danger" type="button"
                                :disabled="codemetaJson.keywords.length <= 1" @click="removeKeyword(i)"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-primary" type="button" @click="addKeyword()">
                                <i class="bi bi-plus-lg"></i> Add Keyword
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">
                        Programming Languages:
                    </label>
                    <div class="col-md-9">
                        <div v-for="(programmingLanguage, i) in codemetaJson.programmingLanguage" class="input-group mb-3">
                            <input class="form-control" list="languages" v-model="codemetaJson.programmingLanguage[i]">
                            <button class="btn btn-outline-danger" type="button"
                                :disabled="codemetaJson.programmingLanguage.length <= 1" @click="removeProgrammingLanguage(i)"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-sm btn-primary" type="button" @click="addProgrammingLanguage()">
                                <i class="bi bi-plus-lg"></i> Add Programming Language
                            </button>
                        </div>
                    </div>
                </div>

                <datalist id="languages">
                    <option v-for="language in languages" :value="language"></option>
                </datalist>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label" for="metadata-form-development-status">
                        Development Status:
                    </label>
                    <div class="col-md-9">
                        <select class="form-select" id="metadata-form-development-status" v-model="codemetaJson.developmentStatus">
                            <option value="">-</option>
                            <option value="Concept">Concept</option>
                            <option value="WIP">WIP</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Abandoned">Abandoned</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Unsupported">Unsupported</option>
                            <option value="Moved">Moved</option>
                        </select>
                    </div>
                </div>
            </div>

            <pre class="mb-3"><code>{{ JSON.stringify(outputJson, null, 4) }}</code></pre>
            <input type="hidden" name="codemetaJson" :value="JSON.stringify(outputJson)">

            <div class="text-center">
                <button class="btn btn-primary" type="submit">
                    Submit
                </button>
            </div>
        </div>
    `,
};

window.initSwhDepositForm = function(root, licenses, languages, initialSwhId) {
    let app = createApp(SwhDepositForm, {
        licenses,
        languages,
        initialSwhId,
    });
    app.config.compilerOptions.whitespace = "preserve";
    app.mount(root);
};
