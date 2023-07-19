<?php if(!isset($templateOk)) die(); ?>
<h3 class="ui centered header">
    <div class="content">
        <i class="fitted plus circle icon"></i>&nbsp;
        <?= $s['addNewBuild'] ?>
    </div>
</h3>

<div class="ui two columns mobile stackable centered grid">
    <div class="column">
        <h3 class="ui header">
            <i class="list icon"></i>
            <?= $s['selectOptions'] ?>
        </h3>
        <form class="ui form" action="fetchupd.php" method="get">
            <div class="field">
                <label><?= $s['arch'] ?></label>
                <select class="ui dropdown"  name="arch">
                    <option value="all">All</option>
                    <option value="amd64" selected>x64 / amd64</option>
                    <option value="x86">x86</option>
                    <option value="arm64">arm64</option>
                </select>
            </div>

            <div class="field">
                <label><?= $s['ring'] ?></label>
                <select class="ui dropdown" name="ring">
                    <option value="msit"><?= $s['channel_msit'] ?></option>
                    <option value="canary" selected><?= $s['channel_canary'] ?></option>
                    <option value="wif"><?= $s['channel_dev'] ?></option>
                    <option value="wis"><?= $s['channel_beta'] ?></option>
                    <option value="rp"><?= $s['channel_releasepreview'] ?></option>
                    <option value="retail"><?= $s['channel_retail'] ?></option>
                </select>
            </div>

            <div class="field">
                <label><?= $s['build'] ?></label>
                <input type="text" value="22621.1" name="build">
            </div>

            <div class="field">
                <label><?= $s['edition'] ?></label>
                <select class="ui dropdown" name="sku">
                    <option value="101">Home</option>
                    <option value="48" selected>Pro</option>
                    <option value="121">Education</option>
                    <option value="4">Enterprise</option>
                    <option value="72">Enterprise Evaluation</option>
                    <option value="125">Enterprise LTSC</option>
                    <option value="129">Enterprise LTSC Evaluation</option>
                    <option value="119">Team</option>
                    <option value="7">Server Standard</option>
                    <option value="8">Server Datacenter</option>
                    <option value="406">Azure Stack HCI</option>
                    <option value="407">Server Datacenter Azure Edition</option>
                </select>
            </div>

            <button class="ui fluid right labeled icon primary button" id="submitForm" type="submit">
                <i class="right arrow icon"></i>
                <?= $s['next'] ?>
            </button>
        </form>

        <div class="ui info message">
            <i class="info icon"></i>
            <?= $s['newBuildNextText'] ?>
        </div>
    </div>

    <div class="column">
        <h3 class="ui header">
            <i class="info circle icon"></i>
            <?= $s['newBuildUsing'] ?>
        </h3>
        <p><?= $s['newBuildUsingText'] ?></p>
        <div class="ui negative icon message">
            <i class="exclamation triangle icon"></i>
            <div class="content">
                <div class="header">
                    <?= $s['optionsNotice'] ?>
                </div>
                <p><?= $s['optionsNoticeText'] ?></p>
            </div>
        </div>
    </div>
</div>

<script>$('select.dropdown').dropdown();</script>
