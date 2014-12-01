<div class="wrapper">
<div class="row">
<div class="col-sm-12 col-md-12 main">
    <h1 class="page-header">Week <?php echo $weekToday; ?> of <?php echo $fullMonth; ?></h1>
    <button class="btn btn-md btn-warning pull-right" data-toggle="modal" data-target="#task-modal">Create Task</button>
    <div class="clearfix"></div>
    <h3 class="text-center">Appointments</h3>
    <table class="table table-condensed table-hover table-bordered wrap-data" id="appointments">
        <tr id="app-header">
            <th>Date</th>
            <th>Title</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Color</th>
            <th>Timeslot</th>
            <th>Actions</th>
        </tr>
        <?php if (!$schedData): ?>
        <tr id="no-record"><td colspan="7" class="text-center">No Appointments</td></tr>
        <?php else: ?>
            <?php foreach ($schedData as $key): ?>
                <tr class="app-datas">
                    <td class="hidden-vals hide">
                        <span id="hv-id"><?php echo $key->id; ?></span>
                        <span id="hv-date"><?php echo $key->date; ?></span>
                        <span id="hv-title"><?php echo $key->title; ?></span>
                        <span id="hv-description"><?php echo $key->description; ?></span>
                        <span id="hv-duration"><?php echo $key->duration; ?></span>
                        <span id="hv-color"><?php echo $key->color; ?></span>
                        <span id="hv-timeslot"><?php echo $key->timeslot; ?></span>
                    </td>
                    <td><?php echo $key->date; ?></td>
                    <td><?php echo $key->title; ?></td>
                    <td><?php echo $key->description; ?></td>
                    <td><?php echo $key->duration; ?> minutes</td>
                    <td><div class="label" style="background-color: <?php echo $key->color; ?>">Label</div></td>
                    <td><?php echo $key->timeslot; ?></td>
                    <td>
                        <a href="#" class="btn btn-xs btn-success m-r-xs" id="edit-app" data-toggle="modal" data-target="#edit-task-modal">Edit</a>
                        <a href="#" class="btn btn-xs btn-dark" data-toggle="modal" data-target="#delete-task-modal" id="delete-app">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <h3 class="text-center">Week Planner</h3>
    <h5 class="text-center">You can drag appointment blocks on this planner and drop it to desired timeslot.</h5>
    <table class="table table-condensed table-hover table-bordered wrap-data">
        <tr>
            <th class="dpdate">Dates</th>
            <th class="dptimeslot">Timeslots</th>
            <th>Duration</th>
        </tr>
        <?php foreach ($weekDates as $key => $value): ?>
            <tr class="wdGrp">
                <?php if ($key == 0): ?>
                    <?php $datePrev = base64_encode(date('Y-m-d', strtotime($value . '- 1 day'))); ?>
                    <a href="<?php echo site_url('users/plannerboard?date='.$datePrev.''); ?>" class="btn btn-warning m-r-xs m-b-xs">Prev</a>
                <?php elseif ($key == 6): ?>
                    <?php $dateNext = base64_encode(date('Y-m-d', strtotime($value . '+ 1 day'))); ?>
                    <a href="<?php echo site_url('users/plannerboard?date='.$dateNext.''); ?>" class="btn btn-warning m-b-xs pull-right">Next</a>
                <?php endif; ?>
                <td class="weekDays"><?php echo $value; ?></td>
                <td>
                    <div class="tl-am">AM</div>
                    <div class="tl-pm">PM</div>
                </td>
                <td style="position: relative;">
                    <ul id="durationAM">
                        <li>8:00</li>
                        <li>9:00</li>
                        <li>10:00</li>
                        <li>11:00</li>
                        <li class="lastli">12:00</li>
                    </ul>
                    <div class="clearfix"></div>

                    <div class="amSlots slots-container"></div>

                    <ul id="durationPM">
                        <li>1:00</li>
                        <li>2:00</li>
                        <li>3:00</li>
                        <li>4:00</li>
                        <li class="lastli">5:00</li>
                    </ul>
                    <div class="clearfix"></div>

                    <div class="pmSlots slots-container"></div>

                </td>
            </tr>    
        <?php endforeach; ?>
    </table>    
</div>
</div>
</div>

<div class="modal" id="task-modal" aria-hidden="false">
    <div class="modal-dialog"> 
        <form action="<?php echo site_url('users/addTask');?>" id="add-task-form" method="post" class="form-horizontal class-forms task-form">
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <button type="button" class="close" data-dismiss="modal">×</button> 
                    <h4 class="modal-title"><i class="fa fa-tasks"></i>&nbsp;&nbsp;Add Task</h4> 
                    <input type="hidden" value="<?php echo $pageDate; ?>" name="dp_pagedate">
                    <input type="hidden" value="" name="dp_id" id="dp_id">
                </div> 
                <div class="modal-body">  
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_date">Date</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_date" class="datepicker-input form-control required" value="<?php echo $pageDate; ?>" name="dp_date" data-date-format="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_title">Title</label>
                        <div class="col-sm-8">
                            <input type="text" name="dp_title" id="dp_title" class="form-control required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_description">Description</label>
                        <div class="col-sm-8">
                            <textarea name="dp_description" id="dp_description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_min_remaining">Min Remaining</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_min_remaining" name="dp_min_remaining" class="form-control disabled required" placeholder="0" disabled="disabled">
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_duration">Duration</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_duration" name="dp_duration" class="form-control required duration-input" placeholder="in minutes">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_color">Tag color</label>
                        <div class="col-sm-8">
                            <select name="dp_color" id="custom-color-select" class="form-control required pull-left m-r" style="width:150px;">
                                <option value="">-- Select Tag Color</option>
                                <option value="#fb6b5b">Red</option>
                                <option value="#ffc333">Yellow</option>
                                <option value="#4cc0c1">Blue</option>
                                <option value="#65bd77">Green</option>
                                <option value="#2e3e4e">Dark Blue</option>
                                <option value="#999999">Gray</option>
                                <option value="" class="cus-opt">Custom</option>
                            </select>
                            <button class="btn btn-default pull-left m-r" id="custom-tag">Tag Color</button>
                            <input type="text" id="dp_color_custom" class="form-control hide pull-left" style="width: 79px;" placeholder="65bd77" maxlength="6">
                            <div class="clearfix"></div>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_timeslot">Timeslot</label>
                        <div class="col-sm-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" class="dp_timeslot" name="dp_timeslot" id="dp_timeslot1" value="AM" checked>AM
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" class="dp_timeslot" name="dp_timeslot" id="dp_timeslot2" value="PM">PM
                                </label>
                            </div>                        
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Save">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>            
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>

<div class="modal" id="edit-task-modal" aria-hidden="false">
    <div class="modal-dialog"> 
        <form action="<?php echo site_url('users/editTask');?>" id="edit-task-form" method="post" class="form-horizontal class-forms task-form">
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <button type="button" class="close" data-dismiss="modal">×</button> 
                    <h4 class="modal-title"><i class="fa fa-magic"></i>&nbsp;&nbsp;Edit Task</h4> 
                    <input type="hidden" value="<?php echo $pageDate; ?>" name="dp_pagedate">
                    <input type="hidden" value="" name="dp_id" id="dp_id">
                </div> 
                <div class="modal-body">  
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_date">Date</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_date" class="datepicker-input form-control required" value="<?php echo $pageDate; ?>" name="dp_date" data-date-format="yyyy-mm-dd">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_title">Title</label>
                        <div class="col-sm-8">
                            <input type="text" name="dp_title" id="dp_title" class="form-control required">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_description">Description</label>
                        <div class="col-sm-8">
                            <textarea name="dp_description" id="dp_description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_min_remaining">Min Remaining</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_min_remaining" name="dp_min_remaining" class="form-control disabled required" placeholder="0" disabled="disabled">
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_duration">Duration</label>
                        <div class="col-sm-8">
                            <input type="text" id="dp_duration" name="dp_duration" class="form-control required duration-input" placeholder="in minutes">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_color">Tag color</label>
                        <div class="col-sm-8">
                            <select name="dp_color" id="custom-color-select" class="form-control required pull-left m-r" style="width:150px;">
                                <option value="">-- Select Tag Color</option>
                                <option value="#fb6b5b">Red</option>
                                <option value="#ffc333">Yellow</option>
                                <option value="#4cc0c1">Blue</option>
                                <option value="#65bd77">Green</option>
                                <option value="#2e3e4e">Dark Blue</option>
                                <option value="#999999">Gray</option>
                                <option value="" class="cus-opt">Custom</option>
                            </select>
                            <button class="btn btn-default pull-left m-r" id="custom-tag">Tag Color</button>
                            <input type="text" id="dp_color_custom" class="form-control hide pull-left" style="width: 79px;" placeholder="65bd77" maxlength="6">
                            <div class="clearfix"></div>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="dp_timeslot">Timeslot</label>
                        <div class="col-sm-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" class="dp_timeslot" name="dp_timeslot" id="dp_timeslot1" value="AM" checked>AM
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" class="dp_timeslot" name="dp_timeslot" id="dp_timeslot2" value="PM">PM
                                </label>
                            </div>                        
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Save">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>            
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>

<div class="modal" id="delete-task-modal" aria-hidden="false">
    <div class="modal-dialog"> 
        <form action="<?php echo site_url('users/deleteTask');?>" id="delete-task-form" method="post" class="form-horizontal class-forms task-form">
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <button type="button" class="close" data-dismiss="modal">×</button> 
                    <h4 class="modal-title"><i class="fa fa-meh-o"></i>&nbsp;&nbsp;Delete Task</h4> 
                    <input type="hidden" value="<?php echo $pageDate; ?>" name="dp_pagedate">
                    <input type="hidden" value="" name="dp_id" id="dp_id">
                    <input type="hidden" value="" name="dp_date" id="dp_date">
                </div> 
                <div class="modal-body">  
                    <h5 class="text-danger text-center">Are you sure you want to delete this task? This is irreversible.</h5>
                </div> 
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Yes">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                </div>            
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div>