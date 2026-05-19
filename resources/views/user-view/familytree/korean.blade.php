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
        height: 130vh;
        overflow: hidden;
        border: 3px solid black;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        /* border-radius: 15px; */
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
        /* background-image: url('/assets/front-end/images/horizontal.jpeg'); */
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
    <div class="container">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left pe-0">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card w-100 d-block d-flex border-0"
                            style="padding: 10px; margin-bottom: 10px; background-color: transparent;">
                            <div class="card-body d-flex align-items-center" style="padding: 0;">
                                
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
                            <div id="family-tree-wrapper" style="width: 100%; height: 100vh; overflow: auto;">
                                <div id="family-tree" class="tree">
                                                            <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900 d-flex align-items-center justify-content-center mb-3 mt-3">{{ $value[0]['familyid'] }}'s Tree</h2>
                                </div>
                            </div>
                        </div>
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
                                        <select class="form-control" name="type">
                                            @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Relation Type:') }}</label>
                                        <select class="form-control" name="type">
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
        .attr("preserveAspectRatio", "xMinYMin meet");

    const svgRoot = svg.append("g");

    fetch(`/user/get-family-tree/{{ $value[0]['id'] }}`)
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                document.getElementById("family-tree").innerHTML =
                    "<h3><br><br><center>No data available</center></h3>";
                return;
            }

            const cardWidth = 280;
            const cardHeight = 180;
            const horizontalSpacing = 10;
            const verticalSpacing = 15;

            let allNodes = [];
            let generations = new Set();

            function placeNode(node, depth = 0, xOffset = 1400) {
                const yPos = 50 + depth * (cardHeight + verticalSpacing);
                node._x = xOffset;
                node._y = yPos;
                node._depth = depth;
                allNodes.push(node);
                generations.add(yPos + cardHeight);

                let currentX = node._x - (cardWidth + horizontalSpacing);

                if (depth === 0 && node.spouses?.length) {
                    node.spouses.forEach(spouse => {
                        spouse._x = currentX;
                        spouse._y = yPos;
                        spouse._depth = depth;
                        allNodes.push(spouse);
                        currentX -= cardWidth + horizontalSpacing;
                    });
                }

                if (depth === 0 && node.siblings?.length) {
                    node.siblings.forEach(sibling => {
                        sibling._x = currentX;
                        sibling._y = yPos;
                        sibling._depth = depth;
                        allNodes.push(sibling);
                        currentX -= cardWidth + horizontalSpacing;
                    });
                }

                let childX = node._x;
                (node.children || []).forEach(child => {
                    const childY = 50 + (depth + 1) * (cardHeight + verticalSpacing);
                    child._x = childX;
                    child._y = childY;
                    child._depth = depth + 1;
                    allNodes.push(child);
                    generations.add(childY + cardHeight);

                    let innerX = child._x - (cardWidth + horizontalSpacing);

                    if (child.spouses?.length) {
                        child.spouses.forEach(spouse => {
                            spouse._x = innerX;
                            spouse._y = childY;
                            spouse._depth = depth + 1;
                            allNodes.push(spouse);
                            innerX -= cardWidth + horizontalSpacing;
                        });
                    }

                    if (child.siblings?.length) {
                        child.siblings.forEach(sibling => {
                            sibling._x = innerX;
                            sibling._y = childY;
                            sibling._depth = depth + 1;
                            allNodes.push(sibling);
                            innerX -= cardWidth + horizontalSpacing;
                        });
                    }

                    placeNode(child, depth + 1, childX);
                    childX = innerX;
                });
            }

            const root = data[0];
            placeNode(root, 0, 1400); // Start from right

            const birthLabel = "{{ __('messages.Birth') }}";
            const deathLabel = "{{ __('messages.Death') }}";
            const Deathplace = "{{ __('messages.Deathplace') }}";
            const Profession = "{{ __('messages.Profession') }}";

            const node = svgRoot.selectAll('g.node')
                .data(allNodes)
                .enter()
                .append('g')
                .attr('class', 'node')
                .attr('transform', d => `translate(${d._x}, ${d._y})`);

            node.append('foreignObject')
                .attr('width', cardWidth)
                .attr('height', cardHeight)
                .html(d => `
                    <div class="d-flex flex-row align-items-start shadow-sm p-2"
                        style="height: ${cardHeight}px; width: ${cardWidth}px; box-sizing: border-box; background-color: white !important; color: black !important; font-size: 17px;">
                        <div style="margin-left: 10px;">
                            <h4 class="fw-bold mb-1" style="font-size: 20px;">
                                ${d.firstname || ''} ${d.lastname || ''}
                            </h4>
                            ${d.birthdate ? `<p class="mb-1" style="font-size: 16px;"><strong>${birthLabel}:</strong> ${d.birthdate}</p>` : ''}
                            ${d.deathdate ? `<p class="mb-1" style="font-size: 16px;"><strong>${deathLabel}:</strong> ${d.deathdate}</p>` : ''}
                            ${d.deathplace ? `<p class="mb-1" style="font-size: 16px;"><strong>${Deathplace}:</strong> ${d.deathplace}</p>` : ''}
                            ${d.profession ? `<p class="mb-1" style="font-size: 16px;"><strong>${Profession}:</strong> ${d.profession}</p>` : ''}
                            <div class="d-flex mt-2" style="font-size: 16px;">
                                ${d.type == '1' || d.type == '4'
                    ? `<a href="javascript:void(0)" data-toggle="modal" data-target="#addnewtree" data-id="${d.id}" data-parent-id="${d.parent_id}" class="me-3"><i class="feather-plus" title="Add"></i></a>`
                    : ''
                }
                                <a href="javascript:void(0)" class="me-3"><i class="feather-edit" title="Edit" data-id="${d.id}"></i></a>
                                <a href="javascript:void(0)"><i class="feather-trash-2" title="Delete" data-id="${d.id}"></i></a>
                            </div>
                        </div>
                    </div>
                `);

            // Adjust viewBox and set explicit SVG dimensions
            const treeMinX = Math.min(...allNodes.map(n => n._x));
            const treeMaxX = Math.max(...allNodes.map(n => n._x + cardWidth));
            const totalHeight = Math.max(...allNodes.map(n => n._y + cardHeight)) + 50;
            const extraRightPadding = cardWidth + 150;
            const totalWidth = treeMaxX - treeMinX + extraRightPadding;

            svg
                .attr("viewBox", `${treeMinX - 100} 0 ${totalWidth} ${totalHeight}`);
            const sortedGenerations = [...generations].sort((a, b) => a - b);
            sortedGenerations.forEach((y, index) => {
                const generationNumber = index + 1;
              const label = `${generationNumber}${getOrdinalSuffix(generationNumber)} {{ __('messages.Generation') }}`;
                const nodesInGen = allNodes.filter(n => n._y + cardHeight === y);
                if (nodesInGen.length === 0) return;

                const rightmostX = Math.max(...nodesInGen.map(n => n._x + cardWidth));
                const boxX = rightmostX + 30;
                const boxY = y - cardHeight + 5;

                const labelGroup = svgRoot.append("g")
                    .attr("transform", `translate(${boxX}, ${boxY})`);

                labelGroup.append("rect")
                    .attr("width", cardWidth)
                    .attr("height", cardHeight - 10)
                    .attr("fill", "white")
                    .attr("stroke", "#ccc")
                    .attr("rx", 8)
                    .attr("ry", 8)
                    .style("filter", "drop-shadow(0px 2px 2px rgba(0,0,0,0.1))");

                labelGroup.append("text")
                    .attr("x", cardWidth / 2)
                    .attr("y", cardHeight / 2)
                    .attr("dominant-baseline", "middle")
                    .attr("text-anchor", "middle")
                    .attr("font-size", "18px")
                    .attr("font-weight", "bold")
                    .attr("fill", "#333")
                    .text(label);

                svgRoot.append("line")
                    .attr("x1", treeMinX - 100)
                    .attr("x2", treeMaxX + 300)
                    .attr("y1", y)
                    .attr("y2", y)
                    .attr("stroke", "#999")
                    .attr("stroke-width", 1.2)
                    .attr("stroke-dasharray", "4,3");
            });

            function getOrdinalSuffix(n) {
                const s = ["th", "st", "nd", "rd"],
                    v = n % 100;
                return s[(v - 20) % 10] || s[v] || s[0];
            }
        })
        .catch(error => {
            console.error("Error:", error);
            document.getElementById("family-tree").innerHTML = "<p>No Data Found</p>";
        });

        function flattenData(data) {
            const result = [];

            function addAll(node, isRoot = false) {
                node.children = node.children || [];
                node.partners = node.partners || [];
                node.exPartners = node.exPartners || [];
                node.spouses = node.spouses || [];
                node.siblings = node.siblings || [];

                if (isRoot) node._isRoot = true;
                result.push(node);

                const spousesToInclude = isRoot ? node.spouses : [];

                const relations = [
                    ...node.children,
                    ...node.partners,
                    ...node.exPartners,
                    ...spousesToInclude,
                    ...node.siblings
                ];

                relations.forEach(child => addAll(child, false));
            }

            data.forEach(root => addAll(root, true));
            return result;
        }

        function buildTree(data) {
            const map = {};
            let rootNode;
            data.forEach(d => map[d.id] = {
                ...d,
                children: []
            });
            data.forEach(d => {
                if (d.parent_id === 0) rootNode = map[d.id];
            });
            data.forEach(d => {
                if (d.parent_id === rootNode.id) rootNode.children.push(map[d.id]);
            });

            function linkChildren(parent) {
                data.forEach(d => {
                    if (d.parent_id === parent.id && d.parent_id !== rootNode.id) {
                        parent.children.push(map[d.id]);
                    }
                });
                parent.children.forEach(linkChildren);
            }
            rootNode.children.forEach(linkChildren);
            rootNode._isRoot = true;
            return rootNode;
        }

        function removeSpousesFromChildren(node) {
            if (!node || !node.children) return;
            node.children.forEach(child => {
                if (child.spouses && child.spouses.length > 0) {
                    child.spouses.forEach(s => s._isSpouse = true);
                    child.spouses = [];
                }
                removeSpousesFromChildren(child);
            });
        }

        function adjustNodesByGeneration(root) {
            const cardWidth = 200;
            const cardHeight = 140;
            const horizontalSpacing = 50;
            const verticalSpacing = 60; // 🔽 reduced from 180 to 60
            // const yPos = 100 + depth * (cardHeight + verticalSpacing);
            const generations = new Map();

            // Group nodes by generation (depth)
            root.each(node => {
                const depth = node.depth;
                if (!generations.has(depth)) generations.set(depth, []);
                generations.get(depth).push(node);
            });

            // Position nodes in rows
            generations.forEach((nodes, depth) => {
                const startX = 100;
                const y = 100 + depth * (cardHeight + verticalSpacing);

                nodes.forEach((node, i) => {
                    node.x = startX + i * (cardWidth + horizontalSpacing);
                    node.y = y;
                });
            });

            // Apply updated positions
            svg.selectAll('.node')
                .attr('transform', d => `translate(${d.x},${d.y})`);
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
                // Insert modal content into the page
                $('body').append(response);
                $('#editMemberModal').modal('show');
                // Update the form action URL
                $('#editMemberForm').attr('action', `/user/members/${memberId}`);
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

        $.ajax({
            url: $(form).attr('action'),
            method: 'POST',
            data: $(form).serialize(),
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
        if (parent_id == undefined) {
            modal.find('.modal-body #self_id').val(0);
        } else {
            modal.find('.modal-body #self_id').val(memberId);
            if (parent_id > 0) {
                modal.find('.modal-body select[name="type"] option[value="4"]').prop('disabled', true);
                modal.find('.modal-body select[name="type"] option[value="1"]').prop('selected', true);
            } else {
                modal.find('.modal-body select[name="type"] option[value="4"]').prop('disabled', false);
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
            .each(function() {
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
            img.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = this.width;
                canvas.height = this.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(this, 0, 0);
                const dataURL = canvas.toDataURL('image/png');
                resolve(dataURL);
            };
            img.onerror = function() {
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