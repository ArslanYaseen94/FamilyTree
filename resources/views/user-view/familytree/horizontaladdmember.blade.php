@extends('layouts.user.app')
@section('content')
@php
$value = \App\Models\FamilyTree::where(['id' => $id])->get();
 $types = \App\Models\Types::where(['is_active' => 1])->get();
 $generations = \App\Models\Generations::where(['is_active' => 1])->get();
@endphp
<style>
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .form-avatar {
        margin-top: 1em;
    }

    .form-avatar .form-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 0em;
    }
.pdf-export-mode {
  background-color: #ffffff !important;
}

/* White background and black text for every card */
.pdf-export-mode .card {
  background-color: #ffffff !important;
  color: #000000 !important;
  border: 2px solid #000 !important;
  box-shadow: none !important;
}

/* Ensure nested text inside cards is black */
.pdf-export-mode .card * {
  color: #000000 !important;
}

    .form-avatar .form-group {
        margin: 0;
        position: relative;
        cursor: pointer;
        width: 61px;
        /* Adjust size as needed */
    }

    .form-avatar .form-group img {
        width: 100%;
        height: auto;
        border: 2px solid transparent;
        border-radius: 50%;
        transition: border-color 0.3s ease;
    }

    .form-avatar .form-group input[type="radio"] {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .form-avatar .form-group input[type="radio"]:checked+label img {
        border-color: #007bff;
        /* Change this color to highlight the selected image */
    }

    .form-avatar .form-group label {
        display: block;
        padding: 5px;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: border-color 0.3s ease;
    }

    .form-avatar .form-group input[type="radio"]:checked+label {
        border-color: #007bff;
        /* Change this color to match the selected state */
    }

    .form-label.tt {
        display: flex;
        align-items: center;
        text-align: center;
        position: relative;
        margin: 1em 0;
    }

    .form-label.tt::before,
    .form-label.tt::after {
        content: '';
        flex: 1;
        border-top: 1px solid #ccc;
        /* Change this color and style as needed */
        margin: 0 1em;
    }


    #family-tree {
        position: relative;
        width: 90%;
        height: 160vh;
        overflow: hidden;
        border: 5px solid #0075ff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        border-radius: 15px;
        cursor: grab;
        margin-left: 3rem;
    }

    #family-tree::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('/assets/front-end/images/horizontal.jpeg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        z-index: 0;
        border-radius: inherit;
    }

    #family-tree>* {
        position: relative;
        z-index: 1;
    }

    .node {
        cursor: pointer;
    }

    .link {
        fill: none;
        stroke: #555;
        stroke-width: 4px;
    }

    .node-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .member-card,
    .partner-card {
        border: 1px solid var(--theme-color) !important;
        height: 140px;
        width: 143px;
        margin: 5px;
    }

    .partner-card {
        border: 3px solid var(--theme-color) !important;
        height: 120px;
        width: 140px;
        border-radius: 70px;
        margin: 5px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
    }


    .avatar {
        width: 90%;
        height: 90%;
        border-radius: 50%;
        border: 3px solid var(--theme-color) !important;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }


    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .partner-card {
        height: 134px !important;
        width: 113px !important;
        border-radius: 50% !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {

        .member-card,
        .partner-card {
            height: 10vh;
            width: 10vw;
        }
    }

    @media (max-width: 992px) {

        .member-card,
        .partner-card {
            height: 8vh;
            width: 8vw;
        }
    }

    @media (max-width: 768px) {

        .member-card,
        .partner-card {
            height: 6vh;
            width: 6vw;
        }
    }

    @media (max-width: 576px) {

        .member-card,
        .partner-card {
            height: 5vh;
            width: 5vw;
        }

        #family-tree {
            height: 60vh;
        }
    }

    .card img {
        object-fit: cover;
    }
</style>

<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card w-100 d-block d-flex border-0"
                        style="padding: 10px; margin-bottom: 10px; background-color: transparent;">
                        <div class="card-body d-flex align-items-center" style="padding: 0;">
                            <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">{{ $value[0]['familyid'] }}'s Tree</h2>
                            <div class="search-form-2 ms-auto">
                                @if (!$hasMembers)
                                <a href="javascript:void(0)"
                                    class="d-none d-lg-block bg-success text-white font-xsssss text-uppercase fw-700 ls-3"
                                    style="padding: 12px;border-radius: 1rem !important;font-size: 13px !important;"
                                    data-toggle="modal" data-target="#addnewtree">New Member</a>

                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="languageSelect">
                                    {{ __('messages.Choose PDF Language') }}:
                                </label>
                                <select id="languageSelect" class="form-select w-auto d-inline-block">
                                    <option value="en" selected> {{ __('messages.English') }}</option>
                                    <option value="zh-CN"> {{ __('messages.Chinese') }}</option>
                                    <option value="ko"> {{ __('messages.Korean') }}</option>
                                </select>
                                <button id="generatePDF" class="btn btn-primary ms-2"> {{ __('messages.Generate PDF') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">


                        <div id="family-tree" class="tree"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<div class="modal fade" id="addnewtree" tabindex="-1" role="dialog" aria-labelledby="addnewtreeLabel"
    aria-hidden="true" style="display:none">
    <div class="modal-dialog" role="document" style="max-width: 45%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-700 mb-0 mt-0 font-md text-grey-900" id="addnewtreeLabel">{{ __('messages.Create FamilyTree') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addmemberform" action="{{ route("user.addmember.store") }}" enctype="multipart/form-data" method="post">
                @csrf
                <div id="form-error" class="alert alert-danger light alert-dismissible fade show" style="display:none;">
                </div>
                <div id="form-success" class="alert alert-success light alert-dismissible fade show"
                    style="display:none;"></div>

                <div class="modal-body">
                    <div class="">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">{{ __('messages.Personal') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">{{ __('messages.Contact') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="bio-tab" data-bs-toggle="tab" data-bs-target="#messages"
                                    type="button" role="tab" aria-controls="messages"
                                    aria-selected="false">{{ __('messages.Biographical') }}</button>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <br>
                            <div role="tabpanel" class="tab-pane active" id="home">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss">{{ __('messages.First Name:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter First Name') }}"
                                            name="firstname" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss">{{ __('messages.Last Name:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Last Name') }}"
                                            name="lastname" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss">{{ __('messages.Email:') }}</label>
                                        <input type="email" class="form-control" placeholder="{{ __('messages.Enter Email') }}"
                                            name="email" />
                                    </div>
                                     <div class="col-md-4 mt-2">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Relation Type:') }}</label>
                                        <select class="form-control" name="type" id="relation_type">
                                            @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                      <div class="col-md-4 mt-2">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Generation:') }}</label>
                                        <select class="form-control" name="generation" id="generation_select">
                                            <option value="">--</option>
                                            @foreach ($generations as $generation)
                                            <option value="{{ $generation->id }}">{{ $generation->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div><br>
                                <div class="row">
                                    <div class="col">
                                        <input class="choice" id="cb1" value="1" name="gender"
                                            type="radio" />
                                        <label class="tgl-btn mr-2" for="cb1">{{ __('messages.Female') }}</label>
                                        <input class="choice" id="cb2" value="2" name="gender"
                                            type="radio" />
                                        <label class="tgl-btn" for="cb2">{{ __('messages.Male') }}</label>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-0">
                                            <input class="tgl tgl-light" id="cbs1" value="1"
                                                name="death" type="checkbox" checked />
                                            <label class="tgl-btn mt-3" for="cbs1"></label>
                                            <label class="form-label">{{ __('messages.This person is alive') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">{{ __('messages.Village:') }}</label>
                                    <input type="text" name="village" placeholder="{{ __('messages.Enter Village') }}"
                                        class="form-control datepicker-here" />
                                </div><br>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ __('messages.Birth Date:') }}</label>
                                        <input type="date" name="birthdate"
                                            class="form-control datepicker-here" data-position='top left' required />
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ __('messages.Marriage Date:') }}</label>
                                        <input type="date" name="marriagedate"
                                            class="form-control datepicker-here" data-position='top left' />
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label">{{ __('messages.Death Date:') }}</label>
                                        <input type="date" name="deathdate"
                                            class="form-control datepicker-here" data-position='top left' />
                                    </div>
                                </div><br>
                                <label class="form-label">{{ __('messages.Link this member to a user:') }}</label>
                                <input type="text" class="form-control" name="user" />
                                <div class="form-avatar">
                                    
                                    <div class="form-inline">
                                        @for ($i = 1; $i <= 18; $i++)
                                            <div class="form-group">
                                                <input type="radio" name="avatar" value="{{ $i }}"
                                                    id="sradioe{{ $i }}" class="choice image">
                                                <label for="sradioe{{ $i }}"><b><img
                                                            src="{{ asset('assets/front-end/avatar/' . $i . '.jpg') }}" /></b></label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="profile">
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Facebook:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Facebook') }}"
                                            name="facebook" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Twitter:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Twitter') }}"
                                            name="twitter" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Instagram:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Instagram') }}"
                                            name="instagram" />
                                    </div>
                                    
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Home Tel:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Home Tel') }}"
                                            name="tel" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Mobile:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Mobile') }}"
                                            name="mobile" />
                                    </div>
                                </div>
                                <label class="form-label mt-2">{{ __('messages.Website:') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('messages.Enter Website') }}"
                                    name="site" />
                            </div>
                            <div role="tabpanel" class="tab-pane" id="messages">
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Birth Place:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Birth Place') }}"
                                            name="birthplace" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label">{{ __('messages.Death Place:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Death Place') }}"
                                            name="deathplace" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Home Town:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Home Town') }}"
                                            name="home_town" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.School:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter School') }}"
                                            name="school" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Background:') }}</label>
                                        <input class="form-control" type="text"
                                            placeholder="{{ __("messages.Enter Background Details") }}" name="background_details" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Business Info:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Business Info') }}"
                                            name="business_info" />
                                    </div>
                                </div>
                               <label class="form-label mt-2"> {{ __('messages.Profession:') }}</label>
                                <textarea class="form-control" name="profession" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Profession") }}"></textarea>
                                <label class="form-label mt-2"> {{ __('messages.Company:') }}</label>
                                <textarea class="form-control" name="company" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Company") }}"></textarea>
                                <label class="form-label mt-2">{{ __('messages.Interests:') }}</label>
                                <textarea class="form-control" name="interests" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Interests") }}"></textarea>
                                <label class="form-label mt-2"> {{ __('messages.Bio Notes:') }}</label>
                                <textarea class="form-control" name="bio" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Bio Notes") }}"></textarea>
                            </div>
                           
                        </div>
                    </div>
                    <input type="hidden" name="family_id" id="family_id" value="{{ $value[0]['id'] }}">
                    <input type="hidden" name="self_id" id="self_id" value="0">
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="bg-current text-center text-white font-xsss fw-600 p-3 w175 rounded-3 d-inline-block">{{ __('messages.Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('scripts')
<script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
<script src="https://d3js.org/d3.v6.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const svg = d3.select("#family-tree").append("svg")
            .attr("width", "100%")
            .attr("height", "100%")
            .attr("viewBox", "0 0 2800 800")
            .attr("preserveAspectRatio", "xMidYMid meet")
            .call(d3.zoom().scaleExtent([0.5, 2]).on("zoom", zoom))
            .append("g")
            .attr("transform", "translate(40,40)");

        function zoom(event) {
            svg.attr("transform", event.transform);
        }

        fetch(`/user/get-family-tree/{{ $value[0]['id'] }}`)
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    document.getElementById("family-tree").innerHTML =
                        "<h3><br><br><center>No data available</center></h3>";
                    return;
                }

                const flatData = flattenData(data);
                console.log(flatData);

                const rootNode = buildTree(flatData);
                console.log(rootNode);
                const root = d3.hierarchy(rootNode, d => d.children);
                console.log(root);

                const treeLayout = d3.tree().size([2800 - 160, 800 - 80]);
                treeLayout(root);

                // Parent-child lines
                svg.selectAll('.link')
                    .data(root.links().filter(d => d.target.data.parent_id === d.source.data.id))
                    .attr('d', d => {
                        const startX = d.source.x + 100;
                        const startY = d.source.y + 100;
                        const endX = d.target.x + 100;
                        const endY = d.target.y;
                        return `M${startX},${startY} V${startY + 10} H${endX} V${endY}`;
                    })
                    .attr("fill", "none")
                    .attr("stroke", "black")
                    .attr("stroke-width", 2);
                const userHasImage = "{{ auth()->user()->has_image }}";
                 const birthLabel = "{{ __('messages.Birth') }}";
                // Node cards (skip virtual root if multiple top-level members exist)
                const node = svg.selectAll('g.node')
                    .data(root.descendants().filter(d => !(d.data && d.data.__virtual)))
                    .enter().append('g')
                    .attr('class', 'node')
                    .attr('transform', d => `translate(${d.x},${d.y})`);

                node.append('foreignObject')
                    .attr('width', 200)
                    .attr('height', 200)
                    .html(d => `
    <div class="card d-flex flex-row align-items-start border shadow-sm p-2"
         style="height: 140px; width: 100%; box-sizing: border-box; border: 3px solid black !important; border-radius: 0;background-color: white !important; color: black !important;">
        ${
            userHasImage === '1' ? `
            <div>
                <div class="me-3" style="width: 40px; height: 40px; overflow: hidden;">
                    ${
                        d.data.photo ? 
                            `<img src="http://127.0.0.1:8000/assets/front-end/Memberimgs/${d.data.photo}" alt="image" class="w-100 h-100 bg-white" style="max-width: 100%; max-height: 100%; object-fit: contain;" crossorigin="anonymous">` 
                            
                        : d.data.avatar ? 
                            `<img src="http://127.0.0.1:8000/assets/front-end/avatar/${d.data.avatar}.jpg" alt="avatar" class="w-100 h-100 bg-white" style="max-width: 100%; max-height: 100%; object-fit: contain;" crossorigin="anonymous">` 
                        : 
                            `<img src="/assets/front-end/default-avatar.jpg" alt="image" class="w-100 h-100 bg-white" crossorigin="anonymous">`
                    }
                </div>
            ` : ''
        }
        <div style="margin-left:20px">
            <h4 class="fw-bold fs-6 mb-1">${d.data.firstname || ''} ${d.data.lastname || ''}</h4>
            ${d.data.birthdate ? `<p class="mb-1"><strong>${birthLabel}</strong> ${d.data.birthdate}</p>` : ''}

            <div class="d-flex mt-2">
                <a href="javascript:void(0)" data-toggle="modal" data-target="#addnewtree" data-id="${d.data.id}" data-parent-id="${d.data.parent_id}" class="me-2">
                    <i class="feather-plus" title="Add"></i>
                </a>
                <a href="javascript:void(0)" class="me-2"><i class="feather-edit" title="Edit" data-id="${d.data.id}"></i></a>
                <a href="javascript:void(0)"><i class="feather-trash-2" title="Delete" data-id="${d.data.id}"></i></a>
            </div>
        </div>
    </div>
`);



                adjustNodesAndLinks(root);
                // drawRelationLines(root);

                const {
                    x: maxX,
                    y: maxY
                } = d3.extent(root.descendants(), d => d.x);
                svg.attr("viewBox", `0 0 ${Math.max(maxX + 160, 2800)} ${Math.max(maxY + 80, 800)}`);
            })
            .catch(error => {
                console.error('Error fetching or processing data:', error);
                document.getElementById("family-tree").innerHTML = "<p>No Data Found</p>";
            });

        // The API now returns a flat list of every member. Nothing to flatten — just pass it through.
        function flattenData(data) {
            return Array.isArray(data) ? data.slice() : [];
        }

        // Build the tree purely from parent_id so EVERY saved member shows up regardless of its
        // relation `type`. Spouses / partners are still attached to dedicated arrays so
        // positionNodeAndDescendants can place them beside the parent.
        function buildTree(data) {
            const map = {};

            data.forEach(d => {
                map[d.id] = {
                    ...d,
                    children: [],
                    partners: [],
                    exPartners: [],
                    spouses: [],
                    siblings: []
                };
            });

            const roots = [];

            data.forEach(d => {
                const node = map[d.id];
                const parent = d.parent_id && map[d.parent_id] ? map[d.parent_id] : null;

                if (!parent) {
                    roots.push(node);
                    return;
                }

                // Every member must be a descendant of its parent so d3.hierarchy can walk to it
                parent.children.push(node);

                // Also categorise so positionNodeAndDescendants can offset them beside the parent
                const t = parseInt(d.type, 10);
                if (t === 2) {
                    parent.partners.push(node);
                } else if (t === 3) {
                    parent.exPartners.push(node);
                } else if (t === 5) {
                    parent.spouses.push(node);
                } else if (t === 6) {
                    parent.siblings.push(node);
                }
            });

            if (roots.length === 0) return null;
            if (roots.length === 1) return roots[0];

            return {
                id: '__virtual_root__',
                __virtual: true,
                firstname: '',
                lastname: '',
                type: 0,
                parent_id: -1,
                children: roots,
                partners: [],
                exPartners: [],
                spouses: [],
                siblings: []
            };
        }

        function adjustNodesAndLinks(root) {
            const spacing = {
                x: 250,
                y: 180
            };
            const rootX = 1200;
            const rootY = 300;
            const cardWidth = 200;
            const cardHeight = 98;

            const positionMap = new Map();
            const nodeMap = new Map(root.descendants().map(n => [n.data.id, n]));

            root.x = rootX;
            root.y = rootY;
            positionMap.set(root.data.id, {
                x: rootX,
                y: rootY
            });

            function drawLine(from, to) {
                if (!from || !to) return;
                const width = cardWidth,
                    height = 140;

                let x1 = from.x + width / 2,
                    y1 = from.y + height / 2;
                let x2 = to.x + width / 2,
                    y2 = to.y + height / 2;

                const dx = x2 - x1,
                    dy = y2 - y1;
                if (Math.abs(dx) > Math.abs(dy)) {
                    x1 = from.x + (dx > 0 ? width : 0);
                    x2 = to.x + (dx > 0 ? 0 : width);
                } else {
                    y1 = from.y + (dy > 0 ? height : 0);
                    y2 = to.y + (dy > 0 ? 0 : height);
                }

                svg.append("path")
                    .attr("class", "custom-link")
                    .attr("d", `M${x1},${y1} L${x2},${y2}`)
                    .attr("stroke", "black")
                    .attr("stroke-width", 2)
                    .attr("fill", "none");
            }

            function positionNodeAndDescendants(node, x, y) {
                node.x = x;
                node.y = y;
                positionMap.set(node.data.id, {
                    x,
                    y
                });

                let lastSpouseX = null,
                    lastSpouseY = null;

                // Only for the root: place first spouse ABOVE the root
                if (node === root && (node.data.spouses || []).length > 0) {
                    const spouse = node.data.spouses[0];
                    const spNode = nodeMap.get(spouse.id);
                    if (spNode) {
                        const sx = x; // horizontally aligned
                        const sy = y - spacing.y; // above the root
                        positionNodeAndDescendants(spNode, sx, sy);
                        lastSpouseX = sx;
                        lastSpouseY = sy;
                    }
                } else {
                    // For non-root nodes, keep spouses on the right
                    (node.data.spouses || []).forEach((spouse, i) => {
                        const spNode = nodeMap.get(spouse.id);
                        if (spNode) {
                            const sx = x + spacing.x;
                            const sy = y + i * spacing.y;
                            positionNodeAndDescendants(spNode, sx, sy);
                            lastSpouseX = sx;
                            lastSpouseY = sy;
                        }
                    });
                }

                const children = (node.data.children || []).filter(c => c.type === 1);
                if (children.length > 0) {
                    let baseX = lastSpouseX !== null ? lastSpouseX + spacing.x : x + spacing.x;
                    let baseY = y;

                    children.forEach((child, i) => {
                        const childNode = nodeMap.get(child.id);
                        if (childNode) {
                            const cx = baseX;
                            const cy = baseY + i * spacing.y;
                            positionNodeAndDescendants(childNode, cx, cy);
                        }
                    });
                }
            }

            // Position root and descendants
            positionNodeAndDescendants(root, rootX, rootY);

            // Position siblings BELOW the root with consistent vertical gaps
            const siblings = root.data.siblings || [];
            const spacingBetweenSiblings = spacing.y;
            const siblingStartY = rootY + spacingBetweenSiblings;

            siblings.forEach((sibling, i) => {
                const sibNode = nodeMap.get(sibling.id);
                if (sibNode) {
                    const sx = rootX;
                    const sy = siblingStartY + i * spacingBetweenSiblings;
                    positionNodeAndDescendants(sibNode, sx, sy);
                }
            });

            svg.selectAll('.custom-link').remove();

            function drawChildrenChain(node) {
                const children = (node.data.children || []).filter(c => c.type === 1);
                if (children.length === 0) return;

                const from = positionMap.get(node.data.id);

                // Draw connection for root: directly root → first child (skip spouse)
                if (node === root) {
                    const firstChild = nodeMap.get(children[0].id);
                    if (firstChild) {
                        const firstChildPos = positionMap.get(firstChild.data.id);
                        drawLine(from, firstChildPos);
                    }
                } else {
                    const spouse = (node.data.spouses || [])[0];
                    if (spouse) {
                        const spousePos = positionMap.get(spouse.id);
                        if (spousePos) {
                            drawLine(from, spousePos);
                            const firstChild = nodeMap.get(children[0].id);
                            if (firstChild) {
                                const firstChildPos = positionMap.get(firstChild.data.id);
                                drawLine(spousePos, firstChildPos);
                            }
                        }
                    } else {
                        const firstChild = nodeMap.get(children[0].id);
                        if (firstChild) {
                            const firstChildPos = positionMap.get(firstChild.data.id);
                            drawLine(from, firstChildPos);
                        }
                    }
                }

                for (let i = 0; i < children.length - 1; i++) {
                    const curr = nodeMap.get(children[i].id);
                    const next = nodeMap.get(children[i + 1].id);
                    if (curr && next) {
                        drawLine(positionMap.get(curr.data.id), positionMap.get(next.data.id));
                    }
                }

                children.forEach(child => {
                    const childNode = nodeMap.get(child.id);
                    if (childNode) drawChildrenChain(childNode);
                });
            }

            drawChildrenChain(root);

            root.descendants().forEach(node => {
                const from = positionMap.get(node.data.id);

                (node.data.spouses || []).forEach(spouse => {
                    drawLine(from, positionMap.get(spouse.id));
                });
                (node.data.partners || []).forEach(partner => {
                    drawLine(from, positionMap.get(partner.id));
                });
                (node.data.exPartners || []).forEach(ex => {
                    drawLine(from, positionMap.get(ex.id));
                });
            });

            // Draw sibling chain: root → first sibling → next sibling → ...
            if (siblings.length > 0) {
                const firstSibNode = nodeMap.get(siblings[0].id);
                const rootPos = positionMap.get(root.data.id);
                const firstSibPos = positionMap.get(firstSibNode.data.id);
                drawLine(rootPos, firstSibPos);

                for (let i = 0; i < siblings.length - 1; i++) {
                    const currNode = nodeMap.get(siblings[i].id);
                    const nextNode = nodeMap.get(siblings[i + 1].id);
                    if (currNode && nextNode) {
                        const currPos = positionMap.get(currNode.data.id);
                        const nextPos = positionMap.get(nextNode.data.id);
                        drawLine(currPos, nextPos);
                    }
                }
            }

            svg.selectAll('.node')
                .attr('transform', d => {
                    const pos = positionMap.get(d.data.id);
                    return pos ? `translate(${pos.x},${pos.y})` : `translate(${d.x},${d.y})`;
                });
        }



    });

    //Edit Member
    $('body').on('click', '.feather-edit', function() {
        let memberId = $(this).data('id');
        $.ajax({
            url: `/user/modal/edit/${memberId}`,
            method: 'GET',
            success: function(response) {
                $('#editMemberModal').remove();
                $('body').append(response);
                $('#editMemberForm').attr('action', `/user/members/${memberId}`);
                $('#editMemberModal').modal('show');
            },
            error: function() {
                alert('An error occurred while fetching the modal.');
            }
        });
    });
    $('body').on('submit', '#editMemberForm', function(e) {
        e.preventDefault();
        var form = this;

        if (!window.validateMemberLifeDates(form)) {
            return;
        }

        var $form = $(form);

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                toastr.success(response.message || '{{ __("messages.Update information successfully") }}');
                $('#editMemberModal').modal('hide');
                setTimeout(function() { window.location.reload(); }, 800);
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    window.showMemberValidationToasts(xhr.responseJSON.errors);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('{{ __("messages.Something went wrong.") }}');
                }
            }
        });
    });

    $('body').on('shown.bs.modal', '#editMemberModal', function() {
        const form = document.getElementById('editMemberForm');
        if (form) window.toggleMemberDeathDateField(form);
    });

    //Delete Member
    $('body').on('click', '.feather-trash-2', function() {
        let memberId = $(this).data('id');
        $.ajax({
            url: '/user/member/' + memberId,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while trying to delete the member.');
            }
        });
    });

    //Add Member
    $('#addnewtree').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var memberId = button.data('id');
        var parent_id = button.data('parent-id');
        var modal = $(this);

        var relationSelect = modal.find('.modal-body select[name="type"]');
        relationSelect.find('option').prop('disabled', false);

        if (parent_id == undefined) {
            modal.find('.modal-body #self_id').val(0);
            relationSelect.find('option').prop('disabled', true);
            relationSelect.find('option[value="4"]').prop('disabled', false).prop('selected', true);
        } else {
            modal.find('.modal-body #self_id').val(memberId);
            relationSelect.find('option[value="4"]').prop('disabled', true);
            relationSelect.prop('selectedIndex', 0);
            if (relationSelect.find('option:selected').is(':disabled')) {
                relationSelect.find('option:not(:disabled)').first().prop('selected', true);
            }
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // Function to handle the avatar selection
    function selectAvatar(avatarId) {
        // Find the radio button with the given ID and check it
        document.getElementById('sradioe' + avatarId).checked = true;
    }

    // Add event listeners to each avatar label
    document.querySelectorAll('.avatar-label').forEach(label => {
        label.addEventListener('click', function() {
            // Extract the avatar ID from the 'for' attribute of the label
            const avatarId = this.getAttribute('for').replace('sradioe', '');
            selectAvatar(avatarId);
        });
    });
    $(document).ready(function() {
        $('#addnewtree').on('shown.bs.modal', function() {
            $('.nav-tabs a[href="#home"]').tab('show');
        });
    });
    document.getElementById('imgbtn').addEventListener('click', function() {
        document.getElementById('file').click();
    });

    document.getElementById('file').addEventListener('change', function() {
        if (this.files.length > 0) {
            // alert('File selected: ' + this.files[0].name);
            $('#imgbtn').html('' + this.files[0].name + '');
        }
    });

    $(document).ready(function() {
        $('#addnewtree').on('shown.bs.modal', function() {
            const form = document.getElementById('addmemberform');
            if (form) window.toggleMemberDeathDateField(form);
        });

        $('#addmemberform').on('submit', function(e) {
            e.preventDefault();
            const form = this;

            if (!window.validateMemberLifeDates(form)) {
                return;
            }

            $('#form-error').hide();
            const $btn = $(form).find('button[type="submit"]');
            $btn.prop('disabled', true);
            const originalHtml = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            var formData = new FormData(form);

            $.ajax({
                url: "{{ route('user.addmember.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                    toastr.success(response.message || '{{ __("messages.Member and user created successfully.") }}');
                    setTimeout(function() { window.location.reload(); }, 1000);
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        window.showMemberValidationToasts(xhr.responseJSON.errors);
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('{{ __("messages.Something went wrong.") }}');
                    }
                }
            });
        });
    });
</script>
@include('partials.member-life-date-validation')
<script>
document.getElementById("generatePDF").addEventListener("click", async () => {
    const lang = document.getElementById("languageSelect").value;
    const treeElement = document.getElementById("family-tree");

    // ✅ Remove add/edit/delete icons
    d3.selectAll('.feather-plus, .feather-edit, .feather-trash-2')
        .each(function () {
            this.parentNode?.remove();
        });

    // ✅ Translate text if needed
    if (lang !== 'en') {
        const textElements = document.querySelectorAll('#family-tree .card h4, #family-tree .card p, #family-tree .card strong');
        for (const el of textElements) {
            const originalText = el.innerText;
            const translated = await translateText(originalText, lang);
            el.innerText = translated;
        }
    }

    // ✅ Convert images to Base64 to avoid CORS/rendering issues
    await replaceImagesWithBase64();

    // ✅ Apply temporary export styles
    treeElement.classList.add("pdf-export-mode");

    // ✅ Delay for layout stabilization
    setTimeout(async () => {
        const canvas = await html2canvas(treeElement, {
            useCORS: true,
            scale: 2,
            backgroundColor: '#ffffff',
            logging: true,
        });

        const imgData = canvas.toDataURL("image/png");
        const pdf = new jspdf.jsPDF({
            orientation: 'landscape',
            unit: 'px',
            format: [canvas.width, canvas.height]
        });

        pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
        pdf.save("family-tree.pdf");

        // ✅ Cleanup export styles
        treeElement.classList.remove("pdf-export-mode");
    }, 100);
});

// ✅ Convert <img src="url"> to Base64 to ensure html2canvas renders them
async function replaceImagesWithBase64() {
    const images = document.querySelectorAll('#family-tree img');
    const promises = Array.from(images).map(async img => {
        const src = img.src;
        try {
            const base64 = await convertImgToBase64URL(src);
            img.setAttribute('src', base64);
        } catch (e) {
            console.warn('Image skipped due to error:', src);
        }
    });
    await Promise.all(promises);
}

// ✅ Helper: Convert image URL to Base64
function convertImgToBase64URL(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = this.width;
            canvas.height = this.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this, 0, 0);
            const dataURL = canvas.toDataURL('image/png');
            resolve(dataURL);
        };
        img.onerror = function () {
            reject(new Error(`Could not load image at ${url}`));
        };
        img.src = url;
    });
}

// ✅ Free translation with LibreTranslate
async function translateText(text, targetLang) {
    const response = await fetch("https://libretranslate.com/translate", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            q: text,
            source: "en",
            target: targetLang,
            format: "text"
        })
    });

    const data = await response.json();
    return data.translatedText || text;
}
</script>


@endsection