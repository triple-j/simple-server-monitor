<!DOCTYPE html>
<html>
<head>
    <title><?=$this->e($title)?></title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.5/flatly/bootstrap.min.css" rel="stylesheet" integrity="sha256-sHwgyDk4CGNYom267UJX364ewnY4Bh55d53pxP5WDug= sha512-mkkeSf+MM3dyMWg3k9hcAttl7IVHe2BA1o/5xKLl4kBaP0bih7Mzz/DBy4y6cNZCHtE2tPgYBYH/KtEjOQYKxA==" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha256-Sk3nkD6mLTMOF0EOpNtsIry+s1CsaqQC1rVLTAy+0yc= sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha256-k2/8zcNbxVIh5mnQ52A0r3a6jAgMGxFJFE2707UxGCk= sha512-ZV9KawG2Legkwp3nAlxLIVFudTauWuBpC10uEafMHYL0Sarrz5A7G79kXh5+5+woxQ5HM559XX2UZjMJ36Wplg==" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-resource.min.js"></script>

    <script>
        var app = angular.module("MyApp", []);

        app.controller("SystemCtrl", function($scope, $http) {
            var getSystemInfo = function() {
                $http.get('<?=$this->e($root)?>info/system').
                    success(function(data, status, headers, config) {
                        $scope.system = data;
                    }).
                    error(function(data, status, headers, config) {
                        // log error
                    });
            };

            getSystemInfo();
            window.setInterval(getSystemInfo, 20000);
        });

        app.controller("CpuCtrl", function($scope, $http) {
            var getCpuInfo = function() {
                $http.get('<?=$this->e($root)?>info/cpu').
                    success(function(data, status, headers, config) {
                        $scope.cpu = data;
                    }).
                    error(function(data, status, headers, config) {
                        // log error
                    });
            };

            getCpuInfo();
            window.setInterval(getCpuInfo, 5000);
        });

    </script>
</head>
<body ng-app="MyApp">
<header>
<nav class="navbar navbar-inverse navbar-static-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">System Info</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="fa fa-cog"></span> Settings</a></li>
            </ul>
        </div>
    </div>
</nav>
</header>

<div class="container">
    <div class="row">
        <div class="col-sm-6 col-lg-4" ng-controller="SystemCtrl">
            <dl>
                <dt>Host</dt>
                <dd>{{system.host}}</dd>
            </dl>
            <dl>
                <dt>Operating System</dt>
                <dd>{{system.os}}</dd>
            </dl>
            <dl>
                <dt>Time</dt>
                <dd>{{system.time}}</dd>
            </dl>
            <dl>
                <dt>Uptime</dt>
                <dd>{{system.uptime}}</dd>
            </dl>
        </div>

        <div class="col-sm-6 col-lg-4" ng-controller="CpuCtrl">
            <div ng-repeat="info in cpu.individual">
                <label>CPU {{info.cpu}}</label>
                <div class="progress">
                    <div class="progress-bar" role="progressbar"
                         aria-valuenow="{{info.user | number:0}}" aria-valuemin="0" aria-valuemax="100"
                         style="width:{{info.user | number:0}}%">
                        {{100 - info.idle | number:0}}%
                    </div>
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         aria-valuenow="{{info.nice | number:0}}" aria-valuemin="0" aria-valuemax="100"
                         style="width:{{info.nice | number:0}}%">
                    </div>
                    <div class="progress-bar progress-bar-warning" role="progressbar"
                         aria-valuenow="{{info.system | number:0}}" aria-valuemin="0" aria-valuemax="100"
                         style="width:{{info.system | number:0}}%">
                    </div>
                </div>
            </div>
        </div>
    <!--/div>
    <div class="row"-->
        <div class="clearfix visible-sm-block visible-md-block"></div>

        <div class="col-sm-6 col-lg-4">
            <div>
                <label>Memory</label>
            </div>
            <div>
                <label>Swap</label>
            </div>
        </div>

        <div class="clearfix visible-lg-block"></div>

        <div class="col-sm-6 col-lg-4">
            <div>
                <label>Network</label>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        ...
    </div>
</footer>

</body>
</html>
