@extends('layouts.vertical', ['title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('content')
    @include('layouts.shared/page-title', ['sub_title' => 'Menu', 'page_title' => 'Dashboard'])
    <div class="dashWrapper" x-data="{
            containerWidth: 0,
            slides: {},
            answers: false,
            certificateId: null,
            showModal: false,
            showHideLang: true,
            stageCount: null,
            completedStages: [],
            packageId: '{{ $packagesOwnedByUser[0]->id }}',
            productId: '{{ $packagesOwnedByUser[0]->product_id }}',
            courseName: '{{ $packagesOwnedByUser[0]->course_name}}',
            status: '{{$packagesOwnedByUser[0]->status}}',
            currentUser: '{{auth()->user()->id}}',
            showProgressBar: 'navigate',
            correctAnswers: [3,3,2,2,3,2,3,1,3,3],
            certificateButton: false,
            showNav: false,
            wrongAnswersCount: 0,
            correctAnswersCount: 0,
            showEye: true,
            showSlider: true,
            submittedAnswers: [],
            answerFeedback: [],
            showHideContent: true,
            showStartTest: false,
            testDone: false,
            message: '',
            testAnswers: 0,
            tryAgainButton: false,
            slideCounter: 0,
            selectedAnswer:'',
            language:'english',
            isActive:1,
            answer: false,
            stage: 1,
            courses:'',
            video: false,
            setShowProgressBar: function(){
                if(this.status === 'purchased'){
                    this.showProgressBar = 'freeze'
                }else{
                    this.showProgressBar = 'navigate'
                }
            },
            showNavButton: function(){
                if(this.showProgressBar === 'freeze'){
                    if(this.stage !== this.stageCount){
                        this.showNav = true
                    }else{
                        this.showNav = false
                    }
                }else{
                    if(!this.testDone){
                     this.showNav = true
                    }
                }
            },
            showBullets: function(){
                if(this.showProgressBar === 'freeze'){
                stage !== stageCount
                }else{
                return true
                }
            },
            showVideo: function(){
                axios.put(`/status/package/${this.packageId}`, {id: this.packageId})
                .then(response => {
                }).catch(error => {
                    console.error(error);
                });
                this.showSlider = false;
                this.showModal = false;
                this.showNav = false;
                this.showEye = false;
                this.video = true;
                this.showProgress = true;
                this.showProgressBar = false;
                let videoPractice = document.getElementById('practiceVideo');
                videoPractice.onended = (event) => {
                  console.log(
                    'Video stopped either because it has finished playing or no further data is available.');
                };
            },
            showHideSlide: function(){
                this.showHideContent = !this.showHideContent
            },
            setLanguage: function(lang)
            {
                this.language = lang
                this.getCourseItems()
            },
            startTest: function()
            {
               this.nextSlide();
               this.showStartTest = false;
               this.tryAgainButton = false
            },
            resetTest: function()
            {
                this.setStage(this.stageCount)
                this.submittedAnswers = [];
                let slider = document.getElementById('courseSlider');
                this.slideCounter = 0;
                slider.style.right = 0 + 'px';
                this.tryAgainButton = false;
                this.showStartTest = true;
            },
            submitAnswer: function()
            {
                if(this.selectedAnswer !== '')
                {
                    this.submittedAnswers.push(this.selectedAnswer)
                    console.log(this.selectedAnswer)
                    this.nextSlide();
                    this.message = '';
                    this.selectedAnswer = '';
                }else
                {
                    this.message = 'Select an answer to go to next question';
                }
            },
            checkResult: function() {
                if (this.submittedAnswers.length === this.correctAnswers.length) {
                    let correctCount = 0;

                    for (let i = 0; i < this.submittedAnswers.length; i++) {
                        if (this.submittedAnswers[i] === this.correctAnswers[i]) {
                            correctCount++;
                            this.answerFeedback.push({ questionNumber: i + 1, correct: true });
                        } else {
                            this.answerFeedback.push({ questionNumber: i + 1, correct: false });
                        }
                    }
                    const percentageCorrect = (correctCount / this.correctAnswers.length) * 100;

                    if (percentageCorrect >= 70) {
                        this.testDone = true;
                        this.tryAgainButton = false;
                        this.showVideo()
                        this.showHideLang = false;
                        // Display correct and wrong answers
                        console.log('Correct Answers:', this.answerFeedback.filter(answer => answer.correct));
                        console.log('Wrong Answers:', this.answerFeedback.filter(answer => !answer.correct));
                    } else {
                        this.correctAnswersCount = this.answerFeedback.filter(feedback => feedback.correct).length;
                        this.wrongAnswersCount = this.answerFeedback.filter(feedback => !feedback.correct).length;
                        this.answers = true;
                        this.tryAgainButton = true;
                        console.log(this.tryAgainButton + 'TRYAGAIN BUTTON');
                    }
                }
            },
            toggleAnswer: function(){
                this.answer = !this.answer
            },
            getLength: function(stage) {
                const stageKey = stage === this.stageCount ? 'test' : `stage_${stage}`;
                return Object.keys(this.courses[stageKey]).length;
            },

            setStage: function(stage, navigate)
            {
                if (navigate){
                    if (this.showProgressBar === 'freeze' && this.completedStages.includes(stage)) {
                        this.stage = stage
                        this.getStageSlides(stage)
                        this.showHideContent = false;
                        this.showStartTest = false;
                        this.isActive = stage
                        let slider = document.getElementById('courseSlider');
                        slider.style.right = 0 + 'px';
                        if(stage === this.stageCount){
                            this.showStartTest = true;
                            this.showHideContent = true;
                            this.tryAgainButton = false;
                        }
                         this.slideCounter = 0;
                         slider.style.right = 0 + 'px';
                         this.showNavButton();
                    }else if(this.showProgressBar === 'navigate'){
                        this.stage = stage
                        this.getStageSlides(stage)
                        this.showHideContent = true;
                        this.showStartTest = false;
                        this.isActive = stage
                        let slider = document.getElementById('courseSlider');
                        slider.style.right = 0 + 'px';
                        if(stage === this.stageCount){
                            this.showStartTest = true;
                            this.showHideContent = true;
                            this.tryAgainButton = false;
                        }
                         this.slideCounter = 0;
                         slider.style.right = 0 + 'px';
                         this.showNavButton();
                    }
                }else{
                    this.stage = stage
                    this.getStageSlides(stage)
                        this.showStartTest = false;
                        this.isActive = stage
                        let slider = document.getElementById('courseSlider');
                        slider.style.right = 0 + 'px';
                        if(stage === this.stageCount){
                            this.showStartTest = true;
                            this.showHideContent = true;
                        }
                         this.slideCounter = 0;
                         slider.style.right = 0 + 'px';
                         this.showNavButton();

                    }

                    console.log('afterelse' + this.stage)
            },
            checkStage: function(){
                console.log(this.stage)
            },
            nextSlide: function()
             {
                this.answer = false
                let slider = document.getElementById('courseSlider');
                this.slideCounter += this.containerWidth;
                let maxPosition = this.containerWidth * this.getLength(this.stage)-1;
                if(this.slideCounter <= maxPosition)
                {
                    slider.style.right = this.slideCounter + 'px';
                }else
                {
                    if(this.stage === this.stageCount)
                    {
                        this.checkResult();
                    }
                    if(this.stage < this.stageCount){
                        this.completedStages.push(this.stage)
                        this.stage++;
                        this.completedStages.push(this.stage)
                    }
                    this.setStage(this.stage, false);
                    this.slideCounter = 0;
                    slider.style.right = 0 + 'px';
                }

            },
            prevSlide: function()
            {
                this.answer = false
                let slider = document.getElementById('courseSlider');
                this.slideCounter -= this.containerWidth;
                let minPosition = this.containerWidth;
                if(this.slideCounter >= minPosition){
                    slider.style.right = this.slideCounter + 'px';
                }else{
                    this.slideCounter = 0;
                    slider.style.right = 0 + 'px';
                }
            },
            setScreen: function(){
                 const courseContainer = document.getElementById('courseContainer');
                 const containerWidth = courseContainer.offsetWidth;
                 this.containerWidth = containerWidth;
            },
            selectCourse: function(){
                console.log(this.productId);
                if(this.productId === '1'){
                    if(this.language === 'english')
                    {
                        axios.get('../data/course.json').then(response => {
                            this.courses = response.data.english;
                            this.setStageCount();
                            this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }else if(this.language === 'spanish')
                    {
                        axios.get('../data/course.json').then(response => {
                            this.courses = response.data.spanish;
                            this.setStageCount();
                             this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }else if(this.language === 'russian')
                    {
                        axios.get('../data/course.json').then(response => {
                            this.courses = response.data.russian;
                            this.setStageCount();
                             this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }else if(this.language === 'romanian')
                    {
                        axios.get('../data/course.json').then(response => {
                            this.courses = response.data.romanian;
                            this.setStageCount();
                             this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }else
                    {
                        axios.get('../data/course.json').then(response => {
                            this.courses = response.data.polish;
                            this.setStageCount();
                            this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }
                }else{
                    this.showHideContent = false;
                    this.showEye = false;
                    this.showHideLang = false;
                    if(this.productId === '2'){
                        axios.get('../data/wh.json').then(response => {
                            this.courses = response.data.english;
                            this.correctAnswers = response.data.correctAnswers
                            this.setStageCount();
                             this.getStageSlides(this.stage)
                        }).catch(error => {
                            console.error(error);
                        });
                    }else if(this.productId === '3'){
                         axios.get('../data/ab.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '4'){
                         axios.get('../data/fw.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '5'){
                         axios.get('../data/fe.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '6'){
                         axios.get('../data/aa.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.correctAnswers = response.data.correctAnswers
                                console.log(this.correctAnswers)
                                this.setStageCount();
                                this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '7'){
                         axios.get('../data/wfa.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '8'){
                         axios.get('../data/os.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '9'){
                         axios.get('../data/ppe.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '10'){
                         axios.get('../data/haccp.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '11'){
                         axios.get('../data/wics.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                                 console.log(this.correctAnswers)
                            }).catch(error => {
                                console.error(error);
                         });
                    }else if(this.productId === '12'){
                         axios.get('../data/fsa.json').then(response => {
                                this.courses = response.data.english;
                                this.correctAnswers = response.data.correctAnswers
                                this.setStageCount();
                                 this.getStageSlides(this.stage)
                            }).catch(error => {
                                console.error(error);
                         });
                    }
                }
            },
            setStageCount: function(){
                this.stageCount = Object.keys(this.courses).length;
            },
            getStageSlides(currentStage) {
                let stageKey = currentStage === Object.keys(this.courses).length ? 'test' : 'stage_' + currentStage;
                this.slides =  this.courses[stageKey];
            },
            getCourseItems: function()
            {
                window.matchMedia('(orientation : landscape)').addEventListener('change', e=>{
                    this.setScreen();
                })
                this.setScreen();
                this.setShowProgressBar();
                this.showNavButton();
                this.selectCourse();
            }
        }"
         x-init="getCourseItems">
        {{--        <div class="modalCourseComplete"  x-show="showModal">--}}
        {{--            <div class="modalTitle" style="text-align: center">Congratulations you passed the test! </div>--}}
        {{--            <div class="modalTitle">IMPORTANT</div>--}}
        {{--            <div class="modalText">Please notice, you must complete your self assessment with our team as required, so you can get the full certificate straight away after that.  This training is covering the full theory and practical part as required by Irish Legislation and regarding that you can use your certificate for any jobs for 3 years after the full course is completed. The self assessment it’s delivered online.--}}
        {{--                <br>--}}

        {{--                <br>--}}
        {{--                It is your responsibility to get in touch with our team as instructed (through WhatsApp chat on +353{{config('app.telephone')}} texts only) to organise the practical part for your Manual Handling Training a.s.a.p ( within 24-48hrs ) and to have your full course done. After that you will receive your certificate via email straight away. Follow the information below regarding the self assessment.--}}
        {{--                <br>--}}
        {{--                <br>--}}
        {{--                Kind Regards--}}
        {{--            </div>--}}
        {{--            <div class="modalTitle">Contact Us Via WhatsApp On this line</div>--}}
        {{--            <div class="modalTitle">+353{{config('app.telephone')}} texts only</div>--}}
        {{--            <div class="modalTitle" style="text-align: center">Press here 👇 to continue</div>--}}
        {{--            <div class="adminButton" style="display: flex; align-items: center; justify-content: center; margin-top: 20px" @click="showVideo">UNDERSTOOD</div>--}}
        {{--        </div>--}}
        <div class="landscape">
            <img src="{{asset('images/banners/landscape.png')}}" alt="">
            <div class="landscapeText">Please rotate your phone</div>
        </div>
        <div class="coursePage" @click="checkStage">
            <div class="selectLang" x-show="showHideLang">
                <div class="langText">Pick a language: </div>
                <div class="langItem" @click="setLanguage('english')"><img src="{{asset('/images/flags/en.png')}}" alt=""></div>
                <div class="langItem" @click="setLanguage('polish')"><img src="{{asset('/images/flags/pl.png')}}" alt=""></div>
                <div class="langItem" @click="setLanguage('romanian')"><img src="{{asset('/images/flags/ro.png')}}" alt=""></div>
                <div class="langItem" @click="setLanguage('russian')"><img src="{{asset('/images/flags/ru.png')}}" alt=""></div>
                <div class="langItem" @click="setLanguage('spanish')"><img src="{{asset('/images/flags/sp.png')}}" alt=""></div>
{{--                <div>|</div>--}}
{{--                <div class="langText" id="hideNav">Hide nav bar</div>--}}
{{--                <label class="switch" id="showHideNav">--}}
{{--                    <input type="checkbox">--}}
{{--                    <span class="slider round"></span>--}}
{{--                </label>--}}
            </div>

            <div class="progressBar" x-show="showProgressBar === 'freeze'">
                <template x-for="(stageItem, index) in stageCount" :key="index">
                    <div class="progresItem" @click="setStage(stageItem, true)" x-bind:class="{ 'isActiveClass': isActive === stageItem }">
                        <span x-text="index === stageCount - 1 ? 'Test' : stageItem"></span>
                    </div>
                </template>
            </div>

            <!-- Navigate ProgressBar -->
            <div class="progressBar" x-show="showProgressBar === 'navigate'">
                <template x-for="(stageItem, index) in stageCount" :key="index">
                    <div x-if="index !== stageCount" class="progresItem" @click="setStage(stageItem, true)" x-bind:class="{ 'isActiveClass': isActive === stageItem }">
                        <span x-text="index === stageCount - 1 ? 'Test' : stageItem"></span>
                    </div>
                </template>
            </div>


            <div class="videoContainer" x-cloak x-show="video">
                <template x-if="productId === '1'">
                    <div class="testPass">
                        <video autoplay muted controls class="practicalVideo" id="practiceVideo">
                            <source src="{{asset('video/practical.mp4')}}" type="video/mp4">
                        </video>
                        <div class="videoTextManual">
                            <div class="modalTitle">IMPORTANT</div>
                            <div class="modalText">Please notice, you must complete your self assessment with our team as required, so you can get the full certificate straight away after that.  This training is covering the full theory and practical part as required by Irish Legislation and regarding that you can use your certificate for any jobs for 3 years after the full course is completed. The self assessment it’s delivered online.
                                <br>

                                <br>
                                It is your responsibility to get in touch with our team as instructed (through WhatsApp chat on +353{{config('app.telephone')}} texts only) to organise the practical part for your Manual Handling Training a.s.a.p ( within 24-48hrs ) and to have your full course done. After that you will receive your certificate via email straight away. Follow the information below regarding the self assessment.
                                <br>
                                <br>
                                Kind Regards
                            </div>
                            <div class="modalTitle">Contact Us Via WhatsApp On this line</div>
                            <div class="modalTitle">+353{{config('app.telephone')}} texts only</div>
                        </div>
                        <div class="videoText">
                            <strong>You have two options for completing the self assessment and to get the full certificate that allows you to use it for any job for 3 years.</strong>
                            <br><br>
                            You can watch the video demonstration attached on our website and record after that your video showing how to lift up and carry the load safely and send it back to us through this chat and we'll check that for you.
                            No need to explain the steps if you don’t feel confident about that. Also,it’s not mandatory to wear special equipment (such as PPE) during this process.You can record it in any work environment or at home if you wish so.<br><br>
                            The steps required are:<br><br>
                            *Assess the area /*Assess the load/*Get a good stable base, feet flat on the floor in the line with your shoulders/*Bend the knees/*Keep your back straight /*Take the load from the floor with a good palm grip (one palm at the front side and another palm at the bottom)/*Hold the load close to your body/*Turn around with your feet,don’t twist your body<br><br>
                            It’s important to lift the load from the floor/ground and place it on the table (or any other surface available ) and then back on the floor by following all the steps above.
                            You can use anything as a load in case you don’t have a box.
                            Please watch the video demonstration as advised.<br><br>
                            You can send your video demonstration to our team through the Whatsapp chat on +353{{config('app.telephone')}} and our instructors will evaluate that for you a.s.a.p.
                            Our team is assisting all our customers with a prompt response during our working hours.Sometimes our team might assist you outside our usual working program but during our fixed hours you will  definitely be assisted without any delay.<br><br>
                            If you wish to book a live video call with one of our instructors  to complete the self assessment please send a text message with your full name and email address that was used for your training and with your request regarding that. All the certificates are emailed to everyone straight away after the full course is fully completed.
                        </div>
                    </div>
                </template>
                <template x-if="productId === '2'">
                    <div class="testPassHeights">
                        <iframe width="80%" height="650" src="https://www.youtube.com/embed/Aza-s-uuaY8" frameborder="0" allowfullscreen></iframe>
                        <div class="videoText">
                            <strong>Congratulations on completing the Working at Heights Training.</strong>
                            You are one step away from your certificate. Please watch the final video that provides a demonstration with brief information on how to wear a harness, front, side and back to secure yourself while working at heights.
                            <br><br>
                            Remember :all the information covered by this training it's for your safety first. You have 3 years free access to your course content and feel free to get back and review anytime you need it.
                            <form x-bind:action="`/certificate/create/${packageId}`" method="POST" x-show="showProgressBar === false" style="margin-top: 20px">
                                @csrf
                                <input type="hidden" name="userId" value="{{auth()->user()->id}}">
                                <input type="hidden" name="productId"  x-bind:value="productId">
                                <button type="submit" class="downCertificate">Get Certificate</button>
                            </form>
                        </div>
                    </div>
                </template>
                <template x-if=" productId === '3' || productId === '4' || productId === '5' || productId === '6' || productId === '7' || productId === '8' || productId === '9' || productId === '10' || productId === '11' || productId === '12'">
                    <div class="videoText">
                        <strong>Congratulations! You have passed the test successfully.</strong>
                        Please press on the button saying "Download Certificate" and get your certificate straight away by email, please check your spam and junk mail just in case to find it.
                        <br><br>
                        Remember, you can access our website anytime by using your personal password & email address and download the certificate or review the course content if you wish so.
                        <br><br>
                        Please do get in touch if we can be of any further assistance!<br><br>
                        Best regards<br>

                        <form x-bind:action="`/certificate/create/${packageId}`" method="POST" x-show="showProgressBar === false'"  style="margin-top: 20px">
                            @csrf
                            <input type="hidden" name="userId" value="{{auth()->user()->id}}">
                            <input type="hidden" name="productId"  x-bind:value="productId">
                            <button type="submit" class="downCertificate">Download Certificate</button>
                        </form>
                    </div>
                </template>
            </div>
            <div class="courseContainer" id="courseContainer"  x-on:landscape="setScreen" >
                <img id="eyeIcon" @click="showHideSlide" x-show="showEye" src="{{asset('images/icons/eye.png')}}" alt="Show hide image">
                <div class="courseSlider" id="courseSlider"  x-cloak x-show="showSlider">
                    <template x-for="(slide, index) in slides"  :key="index">
                        <!-- The courseStage container is shown based on the current stage -->
                        <div class="courseStage" >
                            <!-- Each slide is rendered inside the courseStage container -->
                            <div class="slide" x-bind:style="'background-image: url(../..' + slide.img + '); background-size: 100% 100%; background-position-x: center; background-repeat: no-repeat; width: ' + containerWidth + 'px;'">
                                <div class="slideAnswer" x-show="answer" x-text="slide.answer"></div>
                                <div class="slideContent" x-show="showHideContent">
                                    <div class="slideTitle" x-text="slide.title"></div>
                                    <div class="slideSubText" x-text="slide.content"></div>
                                    <div x-show="stage !== stageCount">
                                        <!-- Bullets for non-test stages -->
                                        <template x-for="bullet in slide.bullets">
                                            <div class="bulletPoint">
                                                <img src="{{asset('images/arrows/right-yellow-arrow.png')}}" alt="">
                                                <div class="bulletText" x-text="bullet"></div>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="stage === stageCount">
                                        <!-- Radio buttons for the test stage -->
                                        <template x-for="(bullet, bulletIndex) in slide.bullets" >
                                            <div class="bulletPoint">
                                                <input type="radio" name="answer" x-on:click="selectedAnswer = bulletIndex + 1">
                                                <div class="bulletText" x-text="bullet"></div>
                                            </div>
                                        </template>
                                    </div>

                                </div>
                                <!-- Controls based on stage type -->
                                <div x-show="stage === stageCount && !showStartTest" @click="submitAnswer()" class="showAnswer">Next</div>

                                <div x-show="showStartTest" @click="startTest" class="showAnswer">Start Test</div>

                                <template x-if="stage !== stageCount">
                                    <!-- Show Answer button for non-test stages -->
                                    <div class="showAnswer" x-show="slide.answer" @click="toggleAnswer">Show Answer</div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="navButtons">
                <div class="navButton"  x-show="showNav" @click="prevSlide">⬅️Previous</div>
                <template x-if="tryAgainButton">
                    <div class="tryAgainDiv">
                        <div class="answers">
                            <div class="answersDiv">
                                <h2>Correct Answers-</h2>
                                <div x-text="correctAnswersCount" style="font-size: 20px"></div>
                            </div>
                            <div class="answersDiv">
                                <h2>Wrong Answers-</h2>
                                <div x-text="wrongAnswersCount" style="font-size: 20px"></div>
                            </div>

                        </div>
                        <div class="tryAgain">Please try Again you dit not pass:</div>
                        <div class="tryAgainButton" @click="resetTest">Try Again The Test</div>
                    </div>
                </template>
                <button class="navButton" x-show="showNav" @click="nextSlide" >Next➡️</button>
            </div>
        </div>
    </div>
    <script src="{{asset("js/course.js")}}" defer></script>
@endsection

@section('script')
    @vite(['resources/js/pages/dashboard.js'])
@endsection
