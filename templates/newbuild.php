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
                    <option value="all">all</option>
                    <option value="amd64" selected>x64 / amd64</option>
                    <option value="x86">x86</option>
                    <option value="arm64">arm64</option>
                </select>
            </div>

            <div class="field">
                <label><?= $s['ring'] ?></label>
                <select class="ui dropdown" name="ring">
                    <option value="msit">MSIT</option>
                    <option value="canary" selected><?= $s['channel_canary'] ?></option>
                    <option value="wif"><?= $s['channel_dev'] ?></option>
                    <option value="wis"><?= $s['channel_beta'] ?></option>
                    <option value="rp"><?= $s['channel_releasepreview'] ?></option>
                    <option value="retail"><?= $s['channel_retail'] ?></option>
                </select>
            </div>

            <div class="field">
                <label><?= $s['branch'] ?></label>
                <select class="ui dropdown" name="branch">
                    <option value="auto" selected><?= $s['autoSelect'] ?></option>
                    <option value="rs_prerelease">rs_prerelease</option>
                    <option value="rs2_release">rs2_release</option>
                    <option value="rs3_release">rs3_release</option>
                    <option value="rs4_release">rs4_release</option>
                    <option value="rs5_release">rs5_release</option>
                    <option value="rs5_release_svc_hci">rs5_release_svc_hci</option>
                    <option value="19h1_release">19h1_release</option>
                    <option value="vb_release">vb_release</option>
                    <option value="fe_release_10x">fe_release_10x</option>
                    <option value="fe_release">fe_release</option>
                    <option value="co_release">co_release</option>
                    <option value="ni_release">ni_release</option>
                    <option value="zn_release">zn_release</option>
                    <option value="ge_release">ge_release</option>
                </select>
            </div>

            <div class="field">
                <label><?= $s['build'] ?></label>
                <input type="text" value="26100.1" name="build">
            </div>

            <div class="field">
                <label><?= $s['edition'] ?></label>
                <select class="ui dropdown" name="sku">
                    <option value="101"><?= $s['edition_CORE'] ?></option>
                    <option value="48"><?= $s['edition_PROFESSIONAL'] ?></option>
                    <option value="121"><?= $s['edition_EDUCATION'] ?></option>
                    <option value="4" selected><?= $s['edition_ENTERPRISE'] ?></option>
                    <option value="72"><?= $s['edition_ENTERPRISEEVAL'] ?></option>
                    <option value="125"><?= $s['edition_ENTERPRISES'] ?></option>
                    <option value="129"><?= $s['edition_ENTERPRISESEVAL'] ?></option>
                    <option value="119"><?= $s['edition_PPIPRO'] ?></option>
                    <option value="7"><?= $s['edition_SERVERSTANDARD'] ?></option>
                    <option value="8"><?= $s['edition_SERVERDATACENTER'] ?></option>
                    <option value="406"><?= $s['edition_SERVERAZURESTACKHCICOR'] ?></option>
                    <option value="407"><?= $s['edition_SERVERTURBINE'] ?></option>
                    <option value="210"><?= $s['edition_WNC'] ?></option>
                </select>
            </div>

            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" name="flags[]" value="thisonly">
                    <label><?= $s['thisOnly'] ?></label>
                </div>
            </div>

            <?php if(uupApiConfigIsTrue('allow_corpnet')): ?>
            <div class="field">
                <div class="ui checkbox">
                    <input type="checkbox" name="flags[]" value="corpnet">
                    <label><?= $s['corpnet'] ?></label>
                </div>
            </div>
            <?php endif; ?>

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
