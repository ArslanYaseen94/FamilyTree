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
        height: 75vh;
        /* Adjust as needed */
        overflow: hidden;
        border: 5px solid #0075ff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        border-radius: 15px;
        cursor: grab;
        margin-left: 3rem
    }

    #family-tree::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('/assets/front-end/images/tree.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.3;
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
        stroke-width: 1.5px;
    }

    .node-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .member-card,
    .partner-card {
        border: 2px solid var(--theme-color) !important;
        width: 120px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        overflow: hidden;
        text-align: center;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    .partner-card .avatar-wrapper {
        width: 60px;
        height: 60px;
        margin: 8px auto 4px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--theme-color);
    }

    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid var(--theme-color) !important;
        margin: 8px auto 4px;
        overflow: hidden;
        display: block;
        position: relative;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .card-details {
        padding: 2px 6px 4px;
        background: rgba(255, 255, 255, 0.97);
    }

    .card-details h4 {
        font-size: 10px !important;
        line-height: 1.3;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 110px;
    }

    .card-details p {
        font-size: 8px !important;
        line-height: 1.2;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #555;
    }

    .icons-container {
        display: flex;
        justify-content: center;
        gap: 6px;
        padding: 3px 4px 6px;
        background: rgba(255, 255, 255, 0.97);
    }

    .icons-container a {
        font-size: 11px;
        color: #555;
    }

    .icons-container a:hover {
        color: var(--theme-color);
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
                                @else
                                @endif
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
<!-- main content -->
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
<script src="https://d3js.org/d3.v6.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const svg = d3.select("#family-tree").append("svg")
            .attr("width", "100%")
            .attr("height", "100%")
            .attr("viewBox", "0 0 2800 800")
            .attr("preserveAspectRatio", "xMidYMid meet")
            .call(d3.zoom().scaleExtent([0.3, 2]).on("zoom", zoom))
            .append("g");

        // Pan / zoom transform is applied to the inner <g>; the static viewBox is set later
        // by fitViewBox() once we know where every card actually lives.
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
                const rootNode = buildTree(flatData);
                const root = d3.hierarchy(rootNode, d => d.children);

                // Run our own hierarchical layout. d3.tree() can't handle variable-width nodes
                // (a person + their partners + ex-partners sit on a single row), so we compute
                // each subtree's required width and centre parents above their children. This
                // guarantees cards never overlap and lines never cross another card.
                const positions = computeCustomLayout(root);

                const userHasImage = "{{ auth()->user()->has_image }}";
                const birthLabel = "{{ __('messages.Birth') }}";

                // Create node g elements (skip the virtual root used to host multiple top-level members)
                const node = svg.selectAll('g.node')
                    .data(root.descendants().filter(d => !(d.data && d.data.__virtual)))
                    .enter().append('g')
                    .attr('class', 'node')
                    .attr('transform', d => {
                        const pos = positions.get(d.data.id);
                        return pos ? `translate(${pos.x},${pos.y})` : 'translate(0,0)';
                    });

                node.append('foreignObject')
                    .attr('width', 130)
                    .attr('height', 160)
                    .attr('x', -15)
                    .attr('y', -10)
                    .html(d => `
                    <div class="partner-card">
                    ${
                    userHasImage === '1' ? `
                            <figure class="avatar">
                                ${d.data.photo ?
                                    `<img src="/assets/front-end/Memberimgs/${d.data.photo}" alt="image">`
                                    : d.data.avatar ?
                                    `<img src="/assets/front-end/avatar/${d.data.avatar}.jpg" alt="image">`
                                    :
                                    `<img src="/assets/front-end/default-avatar.jpg" alt="image">`
                                }
                            </figure>
                        ` : ''
         }
                    <div class="card-details">
                        <h4 class="fw-700">${d.data.firstname || ''} ${d.data.lastname || ''}</h4>
                        ${d.data.birthdate ? `<p><strong>${birthLabel}:</strong> ${d.data.birthdate}</p>` : ''}
                    </div>
                    <div class="icons-container">
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#addnewtree" data-id="${d.data.id}" data-parent-id="${d.data.parent_id}"><i class="feather-plus" title="Add"></i></a>
                        <a href="javascript:void(0)"><i class="feather-edit" title="Edit" data-id="${d.data.id}"></i></a>
                        <a href="javascript:void(0)"><i class="feather-trash-2" title="Delete" data-id="${d.data.id}"></i></a>
                    </div>
                    </div>
                `);

                // Draw parent-child connectors and partner / ex-partner relationship lines
                drawAllConnectors(root, positions);

                // Resize the SVG viewBox to fit every card, with padding on all sides
                fitViewBox(positions);
            })
            .catch(error => {
                console.error('Error fetching or processing data:', error);
                document.getElementById("family-tree").innerHTML = "<p>No Data Found</p>";
            });

        // The API now returns a flat list of every member. Nothing to flatten — just pass it through.
        function flattenData(data) {
            return Array.isArray(data) ? data.slice() : [];
        }

        // Build the tree purely from `parent_id` so EVERY saved member shows up regardless of
        // its relation `type`. Every member is added to its parent's `children` array (so
        // d3.hierarchy actually walks to it), and partners/ex-partners are ALSO recorded in
        // dedicated arrays so the positioning code can offset them beside the parent instead of
        // stacking them below as a child.
        function buildTree(data) {
            const map = {};

            data.forEach(d => {
                map[d.id] = {
                    ...d,
                    children: [],
                    partners: [],
                    exPartners: []
                };
            });

            const roots = [];

            data.forEach(d => {
                const node = map[d.id];
                const parent = d.parent_id && map[d.parent_id] ? map[d.parent_id] : null;

                if (!parent) {
                    // Either parent_id is 0 or the referenced parent doesn't exist
                    roots.push(node);
                    return;
                }

                // Every member is a descendant of its parent for hierarchy traversal
                parent.children.push(node);

                // Tag partners / ex-partners so they can be offset beside the parent
                const t = parseInt(d.type, 10);
                if (t === 2) {
                    parent.partners.push(node);
                } else if (t === 3) {
                    parent.exPartners.push(node);
                }
            });

            if (roots.length === 0) return null;
            if (roots.length === 1) return roots[0];

            // If somehow multiple top-level members exist, wrap them under a virtual root that
            // won't be displayed itself but lets d3 lay them out.
            return {
                id: '__virtual_root__',
                __virtual: true,
                firstname: '',
                lastname: '',
                type: 0,
                parent_id: -1,
                children: roots,
                partners: [],
                exPartners: []
            };
        }

    // -----------------------------------------------------------------
    //  Custom hierarchical layout
    //
    //  Geometry:
    //    - Each card's visual box is 130 (w) × 160 (h).
    //    - The d3 `g.node` is translated to (gx, gy); the foreignObject inside is offset
    //      by (-15, -10), so the visual top-left of the card is at (gx - 15, gy - 10) and
    //      the visual centre is (gx + 50, gy + 70).
    //
    //  Strategy:
    //    - Bottom-up: compute the horizontal width each subtree needs, treating the main
    //      person + ex-partners (left) + partners (right) as a single horizontal "row".
    //    - Top-down: place each subtree inside its allocated width so cards never overlap
    //      with siblings, and centre each parent above its non-partner children.
    // -----------------------------------------------------------------
    const CARD_W = 130;
    const CARD_H = 160;
    const SIBLING_GAP = 60;     // gap between adjacent sibling subtrees
    const PARTNER_GAP = 26;     // gap between partner cards on the same row
    const GENERATION_GAP = 240; // vertical distance between generations

    function isPartnerNode(node) {
        const t = parseInt(node.data.type, 10);
        return t === 2 || t === 3;
    }

    function directChildrenOf(node) {
        return (node.children || []).filter(c => !isPartnerNode(c));
    }

    function computeCustomLayout(root) {
        const positions = new Map();
        const slotW = CARD_W + PARTNER_GAP;

        function computeWidth(node) {
            const partners = (node.data && node.data.partners) || [];
            const exPartners = (node.data && node.data.exPartners) || [];
            const leftExt = CARD_W / 2 + exPartners.length * slotW;
            const rightExt = CARD_W / 2 + partners.length * slotW;

            const children = directChildrenOf(node);
            let childrenW = 0;
            children.forEach((c, i) => {
                childrenW += computeWidth(c) + (i > 0 ? SIBLING_GAP : 0);
            });

            const L = Math.max(leftExt, childrenW / 2);
            const R = Math.max(rightExt, childrenW / 2);

            node._lay = {
                L: L,
                R: R,
                leftExt: leftExt,
                rightExt: rightExt,
                childrenW: childrenW,
                totalW: L + R
            };
            return node._lay.totalW;
        }

        function place(node, leftX, y) {
            const lay = node._lay;
            const partners = (node.data && node.data.partners) || [];
            const exPartners = (node.data && node.data.exPartners) || [];

            const mainCenterX = leftX + lay.L;
            // The card's visual centre is at (gx + 50, gy + 70) because foreignObject is at
            // (-15, -10) inside the g and the card is 130×160. We want the visual centre at
            // mainCenterX, so gx = mainCenterX - 50.
            const gx = mainCenterX - 50;
            const gy = y;

            // The virtual root is invisible: only record positions for real members
            if (!(node.data && node.data.__virtual)) {
                positions.set(node.data.id, { x: gx, y: gy });
            }

            // Ex-partners to the left of main
            for (let i = 0; i < exPartners.length; i++) {
                const exGx = gx - (i + 1) * slotW;
                positions.set(exPartners[i].id, { x: exGx, y: gy });
            }

            // Partners to the right of main
            for (let i = 0; i < partners.length; i++) {
                const pGx = gx + (i + 1) * slotW;
                positions.set(partners[i].id, { x: pGx, y: gy });
            }

            // Children below this group, centred under the main person
            const children = directChildrenOf(node);
            if (children.length === 0) return;

            let childLeftX = mainCenterX - lay.childrenW / 2;
            children.forEach(child => {
                place(child, childLeftX, y + GENERATION_GAP);
                childLeftX += child._lay.totalW + SIBLING_GAP;
            });
        }

        computeWidth(root);
        // If the root is a synthetic virtual node (multiple real roots), start it one
        // generation above so the real roots line up at y = 100.
        const startY = (root.data && root.data.__virtual) ? (100 - GENERATION_GAP) : 100;
        place(root, 0, startY);

        return positions;
    }

    function drawAllConnectors(root, positions) {
        // Clear any previously drawn connectors (e.g. from re-runs)
        svg.selectAll('.link, .relation-line').remove();

        // Visual offsets relative to the g.node translate
        const topY = -10;            // top edge of card
        const bottomY = -10 + CARD_H; // bottom edge of card (150)
        const leftX = -15;           // left edge of card
        const rightX = -15 + CARD_W;  // right edge of card (115)
        const midY = -10 + CARD_H / 2; // vertical centre of card (70)
        const centreX = -15 + CARD_W / 2; // horizontal centre of card (50)

        root.descendants().forEach(node => {
            if (node.data && node.data.__virtual) {
                // virtual root: still walk to its real-root children below
                return;
            }
            const myPos = positions.get(node.data.id);
            if (!myPos) return;

            // Parent → child connectors (only for non-partner direct children)
            const children = directChildrenOf(node);
            children.forEach(child => {
                if (child.data && child.data.__virtual) return;
                const cPos = positions.get(child.data.id);
                if (!cPos) return;

                const startX = myPos.x + centreX;
                const startY = myPos.y + bottomY;
                const endX = cPos.x + centreX;
                const endY = cPos.y + topY;
                const mid = (startY + endY) / 2;

                svg.append('path')
                    .attr('class', 'link')
                    .attr('d', `M${startX},${startY} V${mid} H${endX} V${endY}`)
                    .attr('fill', 'none')
                    .attr('stroke', '#999')
                    .attr('stroke-width', 1.5);
            });

            // Partner connectors (solid horizontal line at card mid-height)
            (node.data.partners || []).forEach(p => {
                const pPos = positions.get(p.id);
                if (!pPos) return;
                svg.append('path')
                    .attr('class', 'relation-line')
                    .attr('d', `M${myPos.x + rightX},${myPos.y + midY} L${pPos.x + leftX},${pPos.y + midY}`)
                    .attr('stroke', '#666')
                    .attr('stroke-width', 2)
                    .attr('fill', 'none');
            });

            // Ex-partner connectors (dashed horizontal line at card mid-height)
            (node.data.exPartners || []).forEach(p => {
                const pPos = positions.get(p.id);
                if (!pPos) return;
                svg.append('path')
                    .attr('class', 'relation-line')
                    .attr('d', `M${myPos.x + leftX},${myPos.y + midY} L${pPos.x + rightX},${pPos.y + midY}`)
                    .attr('stroke', '#aaa')
                    .attr('stroke-width', 2)
                    .attr('stroke-dasharray', '6 4')
                    .attr('fill', 'none');
            });
        });

        // Reorder so the nodes paint on top of the lines (better visual stacking)
        svg.selectAll('g.node').raise();
    }

    function fitViewBox(positions) {
        let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
        positions.forEach(pos => {
            const visualLeft = pos.x - 15;
            const visualRight = pos.x - 15 + CARD_W;
            const visualTop = pos.y - 10;
            const visualBottom = pos.y - 10 + CARD_H;
            if (visualLeft < minX) minX = visualLeft;
            if (visualRight > maxX) maxX = visualRight;
            if (visualTop < minY) minY = visualTop;
            if (visualBottom > maxY) maxY = visualBottom;
        });
        if (!isFinite(minX)) { minX = 0; maxX = 2800; minY = 0; maxY = 1200; }

        const pad = 80;
        const vbX = minX - pad;
        const vbY = minY - pad;
        const vbWidth = Math.max(maxX - minX + 2 * pad, 2800);
        const vbHeight = Math.max(maxY - minY + 2 * pad, 1200);
        d3.select('#family-tree svg').attr('viewBox', `${vbX} ${vbY} ${vbWidth} ${vbHeight}`);
    }
    });

    //Edit Member
    $('body').on('click', '.feather-edit', function() {
        let memberId = $(this).data('id');
        // Fetch modal content
        $.ajax({
            url: `/user/modal/edit/${memberId}`,
            method: 'GET',
            success: function(response) {
                // Remove any previously injected edit modal so values from the new member load freshly
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
                setTimeout(function() {
                    window.location.reload();
                }, 800);
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

        // Reset relation type dropdown so previous selection / disabled state never leaks across opens
        var relationSelect = modal.find('.modal-body select[name="type"]');
        relationSelect.find('option').prop('disabled', false);

        if (parent_id == undefined) {
            // No card was clicked: this is the first/root member of the tree
            modal.find('.modal-body #self_id').val(0);
            // Force the new member to be the root (Parent) and lock the choice
            relationSelect.find('option').prop('disabled', true);
            relationSelect.find('option[value="4"]').prop('disabled', false).prop('selected', true);
        } else {
            // Adding relative of an existing member
            modal.find('.modal-body #self_id').val(memberId);
            // "Parent" (type 4) is only valid when no root exists yet, so disable it here
            relationSelect.find('option[value="4"]').prop('disabled', true);
            // Let the user freely pick the actual relation; do not pre-force "Child"
            relationSelect.prop('selectedIndex', 0);
            // If the first option is now disabled (Parent), move to the next enabled one
            if (relationSelect.find('option:selected').is(':disabled')) {
                relationSelect.find('option:not(:disabled)').first().prop('selected', true);
            }
        }
    });
</script>
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
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
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
@endsection