<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\MemberReport;
use App\Models\CommentReport;

class ReportController extends Controller
{
    function memberReport(Request $request)
    {
        if(session('user')){

            if($request->ajax())
            {
                $result = '';
                $reportId = $request->get('report_id');

                $session = session('user');
                $userId = $session->id;
            
                $result = '<div class="member-report-form" id="memberReportForm-'.$reportId.'">
                <form method="post">
                <label for="subject">Choose Category:</label>
                <select name="subject" id="subject" class="form-control">
                <option value="0" disable selected>select one</option>
                <option value="Hate Speech">Hate Speech</option>
                <option value="Racism">Racism</option>
                <option value="Threats">Threats</option>
                <option value="Vulgar">Vulgar</option>
                <option value="Adult">Adult</option>
                <option value="Others">Others</option>
                </select> <br>
                <label for="description">Report Reason</label>
                <textarea id="description" class="form-control" name="description" rows="4"></textarea>
                <input type="hidden" name="reportId" value="'.$reportId.'"></form><br>
                <button class="save btn btn-dark" type="submit" onclick="reportSave()">Report</button></div>';

                return json_encode($result);
                    
            }
        }
    }
    function memberReportSave(Request $request)
    {
        if($request->ajax())
        {
            $result = '';
            $subject = $request->post('subject');
            $description = $request->post('description');
            $reportId = $request->post('report_id');

            $session = session('user');
            $userId = $session->id;
           
            $report = new MemberReport;
            $report->description = $description;
            $report->subject = $subject;
            $report->report_id = $reportId;
            $report->user_id = $userId;
            $report->save();

            if($report)
            {
                return $report;
            }
                  
        }
    }


    function activityReport(Request $request)
    {
        if(session('user')){

            if($request->ajax())
            {
                $result = '';
                $activityId = $request->get('activity_id');

                $session = session('user');
                $userId = $session->id;
            
                $result = '<div class="report-form" id="reportForm-'.$activityId.'">
                <form method="post">
                <label for="subject">Choose Category:</label>
                <select name="subject" id="subject" class="form-control">
                <option value="0" disable selected>select one</option>
                <option value="Hate Speech">Hate Speech</option>
                <option value="Racism">Racism</option>
                <option value="Threats">Threats</option>
                <option value="Vulgar">Vulgar</option>
                <option value="Adult">Adult</option>
                <option value="Others">Others</option>
                </select>
                <label for="description">Report Reason</label>
                <textarea id="description" class="form-control" name="description" rows="4"></textarea>
                <input type="hidden" name="activityId" value="'.$activityId.'"></form>
                <button class="save" type="submit" data-id="'.$activityId.'" onclick="activityReportSave(this)">Report</button>
                <button class="cancel" data-id="'.$activityId.'" onclick="activityReportCancel(this)">Cancel</button></div>';

                return json_encode($result);
                    
            }
        }
    }
    function activityReportSave(Request $request)
    {
        if($request->ajax())
        {
            $result = '';
            $subject = $request->post('subject');
            $description = $request->post('description');
            $activityId = $request->post('activity_id');

            $session = session('user');
            $userId = $session->id;
           
            $report = new Report;
            $report->description = $description;
            $report->subject = $subject;
            $report->activity_id = $activityId;
            $report->user_id = $userId;
            $report->save();

            if($report)
            {
                return $report;
            }
                  
        }
    }

    // Comment
    function commentReport(Request $request)
    {
        if(session('user')){

            if($request->ajax())
            {
                $result = '';
                $activityId = $request->get('activity_id');
                $commentId = $request->get('comment_id');

                $session = session('user');
                $userId = $session->id;
            
                $result = '<div class="report-form" id="commentReportForm-'.$commentId.'">
                <form method="post">
                <label for="subject">Choose Category:</label>
                <select name="subject" id="subject" class="form-control">
                <option value="0" disable selected>select one</option>
                <option value="Hate Speech">Hate Speech</option>
                <option value="Racism">Racism</option>
                <option value="Threats">Threats</option>
                <option value="Vulgar">Vulgar</option>
                <option value="Adult">Adult</option>
                <option value="Others">Others</option>
                </select>
                <label for="description">Report Reason</label>
                <textarea id="description" class="form-control" name="description" rows="4"></textarea>
                <input type="hidden" name="activityId" value="'.$activityId.'">
                <input type="hidden" name="commentId" value="'.$commentId.'"></form>
                <button class="save" type="submit"  activity-id="'.$activityId.'" data-id="'.$commentId.'" onclick="commentReportSave(this)">Report</button>
                <button class="cancel" data-id="'.$commentId.'" onclick="commentReportCancel(this)">Cancel</button></div>';
                   
                return json_encode($result);
            }
        }
    }
    function commentReportSave(Request $request)
    {
        if($request->ajax())
        {
            $result = '';
            $subject = $request->post('subject');
            $description = $request->post('description');
            $activityId = $request->post('activity_id');
            $commentId = $request->post('comment_id');

            $session = session('user');
            $userId = $session->id;
           
            $report = new commentReport;
            $report->description = $description;
            $report->subject = $subject;
            $report->activity_id = $activityId;
            $report->comment_id = $commentId;
            $report->user_id = $userId;
            $report->save();

            if($report)
            {
                return $report;
            }
                  
        }
    }
}
