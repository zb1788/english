webpackJsonp([1], {
        HToJ: function(t, e, i) {
            t.exports = i.p + "static_ksk/img/SignOut.b5ae59d.png"
        },
        I6m9: function(t, e) {},
        NHnr: function(t, e, i) {
            "use strict";
            Object.defineProperty(e, "__esModule", {
                value: !0
            });
            var n = i("IvJb"),
                s = i("sEFh"),
                o = {
                    render: function() {
                        var t = this,
                            e = t.$createElement,
                            i = t._self._c || e;
                        return i("div", {
                                attrs: {
                                    id: "app"
                                }
                            },
                            [i("Head", {
                                attrs: {
                                    time: t.time,
                                    now: t.count + 1,
                                    total: t.questionJson.length,
                                    isBegTime: t.isStart
                                },
                                on: {
                                    gameOver: t.gameOver,
                                    goback: t.goback
                                }
                            }), t._v(" "), i("section", [1 == t.question.quesType ? i("Select", {
                                attrs: {
                                    question: t.question
                                },
                                on: {
                                    next: t.addCount
                                }
                            }) : t._e(), t._v(" "), 2 == t.question.quesType ? i("TianKong", {
                                attrs: {
                                    question: t.question
                                },
                                on: {
                                    next: t.addCount
                                }
                            }) : t._e()], 1), t._v(" "), t.isShow ? i("Dialog", {
                                attrs: {
                                    gonggao: t.gonggao
                                },
                                on: {
                                    startGame: t.startGame
                                }
                            }) : t._e(), t._v(" "), t.isBack ? i("Confirm", {
                                on: {
                                    isback: t.isGoback
                                }
                            }) : t._e()], 1)
                    },
                    staticRenderFns: []
                };
            var a = function(t) {
                    i("esgE")
                },
                r = i("C7Lr")(s.a, o, !1, a, null, null).exports,
                c = i("aozt"),
                u = i.n(c);
            i("e5r7"),
                i("njf+"),
                i("I6m9");
            n.a.config.productionTip = !1,
                n.a.prototype.$http = u.a,
                new n.a({
                    el: "#app",
                    components: {
                        App: r
                    },
                    template: "<App/>"
                })
        },
        OAI1: function(t, e) {},
        "XG+O": function(t, e, i) {
            "use strict";
            var n = i("HToJ"),
                s = i.n(n),
                o = {
                    data: function() {
                        return {
                            img: s.a
                        }
                    },
                    methods: {
                        isback: function(t) {
                            this.$emit("isback", t)
                        }
                    }
                },
                a = {
                    render: function() {
                        var t = this,
                            e = t.$createElement,
                            i = t._self._c || e;
                        return i("div", [i("div", {
                            staticStyle: {
                                display: "block"
                            },
                            attrs: {
                                id: "bg"
                            }
                        }), t._v(" "), i("div", {
                                staticClass: "deTanImg"
                            },
                            [i("div", {
                                    staticClass: "posR"
                                },
                                [i("img", {
                                    staticClass: "img100",
                                    attrs: {
                                        src: t.img
                                    }
                                })]), t._v(" "), i("a", {
                                    staticClass: "ktBtn kyello",
                                    on: {
                                        click: function(e) {
                                            t.isback(1)
                                        }
                                    }
                                },
                                [t._v("退出")]), t._v(" "), i("a", {
                                    staticClass: "ktBtn kgreen",
                                    on: {
                                        click: function(e) {
                                            t.isback(0)
                                        }
                                    }
                                },
                                [t._v("取消")])])])
                    },
                    staticRenderFns: []
                };
            var r = i("C7Lr")(o, a, !1,
                function(t) {
                    i("xMf3")
                },
                null, null);
            e.a = r.exports
        },
        dLxl: function(t, e) {},
        deIM: function(t, e, i) {
            "use strict";
            var n = {
                render: function() {
                    var t = this.$createElement,
                        e = this._self._c || t;
                    return e("div", [e("div", {
                        staticStyle: {
                            display: "block"
                        },
                        attrs: {
                            id: "bg"
                        }
                    }), this._v(" "), e("div", {
                            staticClass: "cgbd pad10",
                            staticStyle: {
                                display: "block"
                            }
                        },
                        [e("div", {
                            staticClass: "pad10",
                            domProps: {
                                innerHTML: this._s(this.gonggao)
                            }
                        }), this._v(" "), e("div", {
                                staticClass: "boxCon"
                            },
                            [e("a", {
                                    staticClass: "btn bDefault closeCG",
                                    on: {
                                        click: this.startGame
                                    }
                                },
                                [e("i", {
                                    staticClass: "icon-kwdd2"
                                }), this._v("开始练习")])])])])
                },
                staticRenderFns: []
            };
            var s = i("C7Lr")({
                    props: ["gonggao"],
                    methods: {
                        startGame: function() {
                            this.$emit("startGame")
                        }
                    }
                },
                n, !1,
                function(t) {
                    i("OAI1")
                },
                null, null);
            e.a = s.exports
        },
        e5r7: function(t, e) {},
        esgE: function(t, e) {},
        i5qY: function(t, e, i) {
            "use strict";
            var n, s = i("AA3o"),
                o = i.n(s),
                a = i("xSur"),
                r = i.n(a),
                c = function() {
                    function t(e, i) {
                        o()(this, t),
                            this.el = e,
                            this.speed = i,
                            this.canvas = document.getElementById(this.el),
                            this.context = this.canvas.getContext("2d"),
                            this.centerX = this.canvas.width / 2,
                            this.centerY = this.canvas.height / 2,
                            this.rad = 2 * Math.PI / this.speed,
                            this.r = 18,
                            this.lineColor = "#fff"
                    }
                    return r()(t, [{
                        key: "blueCircle",
                        value: function(t) {
                            this.context.save(),
                                this.context.strokeStyle = "#19a0a7",
                                this.context.lineWidth = 3,
                                this.context.beginPath(),
                                this.context.arc(this.centerX, this.centerY, this.r, -Math.PI / 2, -Math.PI / 2 + t * this.rad, !1),
                                this.context.stroke(),
                                this.context.closePath(),
                                this.context.restore()
                        }
                    },
                        {
                            key: "whiteCircle",
                            value: function() {
                                this.context.save(),
                                    this.context.beginPath(),
                                    this.context.lineWidth = 4,
                                    this.context.strokeStyle = this.lineColor,
                                    this.context.arc(this.centerX, this.centerY, this.r, 0, 2 * Math.PI, !1),
                                    this.context.stroke(),
                                    this.context.closePath(),
                                    this.context.restore()
                            }
                        },
                        {
                            key: "text",
                            value: function(t) {
                                this.context.save(),
                                    this.context.strokeStyle = this.lineColor,
                                    this.context.font = "20px Arial",
                                    this.speed > 99 ? this.context.strokeText(t.toFixed(0), this.centerX - 18, this.centerY + 8) : this.speed > 9 && this.speed < 100 ? this.context.strokeText(t.toFixed(0), this.centerX - 10, this.centerY + 8) : this.context.strokeText(t.toFixed(0), this.centerX - 5, this.centerY + 8),
                                    this.context.stroke(),
                                    this.context.restore()
                            }
                        },
                        {
                            key: "drawFrame",
                            value: function() {
                                var t = this,
                                    e = setInterval(function() {
                                            t.speed >= 0 ? (t.context.clearRect(0, 0, t.canvas.width, t.canvas.height), t.whiteCircle(), t.text(t.speed), t.blueCircle(t.speed), t.speed -= 1, t.speed <= 10 && (t.context.strokeStyle = "#ffef6a", t.speed)) : clearInterval(e)
                                        },
                                        1e3)
                            }
                        }]),
                        t
                } (),
                u = {
                    props: ["time"],
                    data: function() {
                        return {}
                    },
                    mounted: function() {
                        n = new c("canvas", this.time);
                        var t = this,
                            e = setInterval(function() {
                                    n.speed >= 0 ? (n.speed <= 10 && (n.lineColor = "#ffef6a"), n.context.clearRect(0, 0, n.canvas.width, n.canvas.height), n.whiteCircle(), n.text(n.speed), n.blueCircle(n.speed), n.speed -= 1) : (clearInterval(e), t.gameOver())
                                },
                                1e3)
                    },
                    methods: {
                        gameOver: function() {
                            this.$emit("gameOver")
                        }
                    }
                },
                l = {
                    render: function() {
                        this.$createElement;
                        this._self._c;
                        return this._m(0)
                    },
                    staticRenderFns: [function() {
                        var t = this.$createElement,
                            e = this._self._c || t;
                        return e("div", [e("canvas", {
                            staticStyle: {
                                "border-radius": "100%",
                                "vertical-align": "middle"
                            },
                            attrs: {
                                id: "canvas",
                                width: "100",
                                height: "40"
                            }
                        })])
                    }]
                };
            var h = {
                    components: {
                        Process: i("C7Lr")(u, l, !1,
                            function(t) {
                                i("dLxl")
                            },
                            null, null).exports
                    },
                    props: ["time", "now", "total", "isBegTime"],
                    computed: {
                        isStart: function() {
                            return this.time > 0
                        }
                    },
                    methods: {
                        gameOver: function() {
                            this.$emit("gameOver")
                        },
                        goback: function() {
                            this.$emit("goback")
                        }
                    }
                },
                d = {
                    render: function() {
                        var t = this,
                            e = t.$createElement,
                            i = t._self._c || e;
                        return i("header", {
                                staticClass: "head"
                            },
                            [i("a", {
                                    staticClass: "head-left",
                                    on: {
                                        click: t.goback
                                    }
                                },
                                [i("i", {
                                    staticClass: "icon-back"
                                }), t._v("返回")]), t._v(" "), i("h1", [!t.isBegTime && t.isStart ? i("Process", {
                                attrs: {
                                    time: t.time
                                },
                                on: {
                                    gameOver: t.gameOver
                                }
                            }) : t._e()], 1), t._v(" "), i("a", {
                                    staticClass: "head-right"
                                },
                                [t._v(t._s(t.now) + "/" + t._s(t.total))])])
                    },
                    staticRenderFns: []
                };
            var f = i("C7Lr")(h, d, !1,
                function(t) {
                    i("lE1W")
                },
                null, null);
            e.a = f.exports
        },
        lE1W: function(t, e) {},
        mA23: function(t, e) {},
        "njf+": function(t, e) {},
        "nt/v": function(t, e) {},
        sEFh: function(module, __webpack_exports__, __webpack_require__) {
            "use strict";
            var __WEBPACK_IMPORTED_MODULE_0__components_Head__ = __webpack_require__("i5qY"),
                __WEBPACK_IMPORTED_MODULE_1__components_Select__ = __webpack_require__("wi0O"),
                __WEBPACK_IMPORTED_MODULE_2__components_TianKong__ = __webpack_require__("yp5x"),
                __WEBPACK_IMPORTED_MODULE_3__components_Dialog__ = __webpack_require__("deIM"),
                __WEBPACK_IMPORTED_MODULE_4__components_Confirm__ = __webpack_require__("XG+O");
            function GetQueryString(t) {
                var e = new RegExp("(^|&)" + t + "=([^&]*)(&|$)"),
                    i = window.location.search.substr(1).match(e);
                return null != i ? decodeURI(decodeURI(unescape(i[2]))) : null
            }
            function checkLocalStorage() {
                if (!window.localStorage) return mui.toast("您的手机不支持预览功能"),
                    !1
            }
            function setLocalStorage(t, e) {
                checkLocalStorage(),
                    window.localStorage.setItem(t, e)
            }
            function getLocalStorage(t) {
                checkLocalStorage();
                var e = window.localStorage.getItem(t);
                return decodeURI(e)
            }
            var stageid = GetQueryString("stageid"),
                genreid = GetQueryString("genreid"),
                userstageid = GetQueryString("userstageid"),
                type = GetQueryString("type"),
                urlCallBack = GetQueryString("urlCallBack"),
                title = GetQueryString("title"),
                gradeid = GetQueryString("gradeid"),
                subjectid = GetQueryString("subjectid"),
                moduleid = GetQueryString("moduleid"),
                ks_short_name = getLocalStorage("ks_short_name");
            if (1 == type) var backToStageUrl = "stagelistksk?genreid=" + genreid + "&gradeid=" + gradeid + "&subjectid=" + subjectid + "&moduleid=" + moduleid + "&urlCallBack=" + encodeURIComponent(urlCallBack),
                backToStageUrl = urlCallBack;
            else var backToStageUrl = "genreting?gradeid=" + gradeid + "&subjectid=0002&moduleid=" + moduleid + "&urlCallBack=" + encodeURIComponent(urlCallBack);
            var debug = "zaixian";
            if ("zaixian" == debug) var getUrl = "../Shuxue/Api/getQuesView?stageid=" + stageid + "&type=" + type;
            if ("dev" == debug) var getUrl = "api/getQues";
            if ("houtai" == debug) {
                var id = GetQueryString("id");
                if ("ques" == type) var getUrl = "../Index/getQuesAloneView?quesid=" + id;
                else var getUrl = "../Index/getQuesView?stageid=" + id
            }
            function play() {
                count = 0;
                try {
                    console.log(domain + "nan/" + voiceArr[count] + ".mp3"),
                        UXinJSInterfaceSpeech.playAudio(domain + "nan/" + voiceArr[count] + ".mp3")
                } catch(t) {}
            }
            __webpack_exports__.a = {
                name: "App",
                components: {
                    Head: __WEBPACK_IMPORTED_MODULE_0__components_Head__.a,
                    Select: __WEBPACK_IMPORTED_MODULE_1__components_Select__.a,
                    TianKong: __WEBPACK_IMPORTED_MODULE_2__components_TianKong__.a,
                    Dialog: __WEBPACK_IMPORTED_MODULE_3__components_Dialog__.a,
                    Confirm: __WEBPACK_IMPORTED_MODULE_4__components_Confirm__.a
                },
                data: function() {
                    return {
                        questionArr: [],
                        questionJson: "",
                        question: {
                            id: "",
                            quesType: "",
                            info: ""
                        },
                        isShow: !1,
                        isBack: !1,
                        gonggao: "",
                        time: 0,
                        count: 0,
                        isGameOver: !1,
                        rightNum: 0,
                        stageid:0,
                    }
                },
                computed: {
                    isStart: function() {
                        return ! (this.questionJson.length > 0) || this.isShow
                    }
                },
                methods: {
                    getQuestion: function getQuestion() {
                        var self = this;
                        this.$http.get(getUrl).then(function(response) {
                            self.stageid = response.data[0].stageid,
                            self.time = 1 * parseInt(response.data[0].totaltime),
                                "" == response.data[0].remark ? self.isShow = !1 : (self.isShow = !0, self.gonggao = response.data.stage[0].remark),
                                self.questionJson = response.data,
                                self.question.id = response.data[self.count].id,
                                self.question.quesType = response.data[self.count].questype,
                                self.question.info = eval("(" + response.data[self.count].content + ")"),
                            2 == type && (self.question.voice = eval("(" + self.questionJson[self.count].voice + ")"), void 0 !== self.question.voice && (voiceArr = self.question.voice, self.isShow || play()))
                        }).
                        catch(function(t) {
                            console.log(t)
                        })
                    },
                    getNextQuestion: function getNextQuestion() {
                        this.question.id = this.questionJson[this.count].id,
                            this.question.quesType = this.questionJson[this.count].questype,
                            this.question.info = eval("(" + this.questionJson[this.count].content + ")"),
                        2 == type && (this.question.voice = eval("(" + this.questionJson[this.count].voice + ")"), void 0 !== this.questionJson[this.count].voice && (voiceArr = this.question.voice, play()))
                    },
                    addCount: function(t, e) {
                        try {
                            UXinJSInterfaceSpeech.stopAudio()
                        } catch(t) {}
                        1 == e && this.rightNum++,
                            this.addQuestionRecord(t, e);
                        var i = this;
                        if (this.count < this.questionJson.length - 1) setTimeout(function() {
                                i.count++,
                                i.isGameOver || i.getNextQuestion()
                            },
                            1e3);
                        else {
                            if ("houtai" == debug) return alert("结束"),
                                !1;
                            var stageid = this.stageid;
                            this.$http.get("../Shuxue/Api/submitExams").then(function(t) {}).
                            catch(function(t) {
                                console.log(t)
                            })
                            window.layer.open({
                                content: '试题正在提交...'
                                ,skin: 'msg'
                                ,time: 2 //2秒后自动关闭
                            });
                            //提交数据到后台

                            setTimeout(function(){
                                window.location.href = "../kousuan/wxcs.html";
                            },2000)
                            //window.location.href = "result?rightNum=" + this.rightNum + "&stageid=" + stageid + "&type=" + type + "&userstageid=" + userstageid + "&total=" + this.questionJson.length + "&genreid=" + genreid + "&moduleid=" + moduleid + "&urlCallBack=" + encodeURIComponent(urlCallBack)
                        }
                    },
                    gameOver: function() {
                        if ("houtai" == debug) return alert("时间到");
                        window.layer.open({
                            content: '考试时间已到，试题正在自动提交...'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                        setTimeout(function(){
                            window.location.href = "../kousuan/wxcs.html";
                        },2000)
                        //window.location.href = "result?rightNum=" + this.rightNum + "&stageid=" + stageid + "&type=" + type + "&userstageid=" + userstageid + "&total=" + this.questionJson.length + "&genreid=" + genreid + "&moduleid=" + moduleid + "&urlCallBack=" + encodeURIComponent(urlCallBack),
                            //this.isGameOver = !0
                    },
                    startGame: function() {
                        2 == type && play(),
                            this.isShow = !1
                    },
                    goback: function() {
                        var self = this;
                        window.layer.open({
                            content: '考试没有结束，确定返回吗？'
                            ,btn: ['是的', '不是']
                            ,yes: function(index){
                                layer.close(index);
                                window.location.href = "../kousuan/wxcs.html";
                                // self.$http.get("../Shuxue/Api/submitExams").then(function(t) {
                                //
                                // }).catch(function(t) {
                                //     console.log(t)
                                // })
                            },

                        });
                        //document.getElementsByTagName("body")[0].className = "bover",
                        //this.isBack = !0
                    },
                    isGoback: function(t) {
                        debugger
                        document.getElementsByTagName("body")[0].className = "",
                            0 == t ? this.isBack = !1 : (this.isBack = !1, window.location.href = backToStageUrl)
                    },
                    addQuestionRecord: function(t, e) {
                        var stageid = this.stageid;
                        this.$http.get("../Shuxue/Api/addUserAnswer?stageid=" + stageid  + "&quesid=" + t + "&isright=" + e ).then(function(t) {}).
                        catch(function(t) {
                            console.log(t)
                        })
                    }
                },
                mounted: function() {
                    this.getQuestion();
                    //添加计时器查看当前试题是否结束
                }
            }
        },
        wi0O: function(t, e, i) {
            "use strict";
            var n = i("Eqm3"),
                s = i.n(n),
                o = {
                    props: ["question"],
                    data: function() {
                        return {
                            filter: 0,
                            filterRight: -1,
                            filterWrong: -1,
                            answerArr: ["A", "B", "C", "D", "E", "F"],
                            isComplete: !1
                        }
                    },
                    computed: {},
                    methods: {
                        choose: function(t) {
                            if (!this.isComplete) {
                                if ("string" == typeof this.question.info.answer) var e = this.question.info.answer;
                                else e = this.question.info.answer[0];
                                this.question.info.quesChoice[t].flag == e ? (console.log("right"), this.filterRight = t, this.$emit("next", this.question.id, 1)) : (console.log("wrong"), this.filterWrong = t, this.filterRight = this.findEleInArr(e, this.answerArr), this.$emit("next", this.question.id, 0)),
                                    this.isComplete = !0;
                                var i = this;
                                setTimeout(function() {
                                        i.filterRight = -1,
                                            i.filterWrong = -1,
                                            i.isComplete = !1
                                    },
                                    1e3)
                            }
                        },
                        findEleInArr: function(t, e) {
                            return s.a || (Array.prototype.indexOf = function(t) {
                                for (var e = 0; e < this.length; e++) if (this[e] == t) return e;
                                return - 1
                            }),
                                e.indexOf(t)
                        }
                    }
                },
                a = {
                    render: function() {
                        var t = this,
                            e = t.$createElement,
                            i = t._self._c || e;
                        return i("div", {
                                staticClass: "timu",
                                staticStyle: {
                                    display: "block"
                                }
                            },
                            [i("h5", {
                                staticClass: "textH5",
                                domProps: {
                                    innerHTML: t._s(t.question.info.quesName)
                                }
                            }), t._v(" "), "" !== t.question.info.quesContent ? [1 === t.question.info.quesContentType ? i("p", {
                                    staticClass: "fenx"
                                },
                                [t._v(t._s(t.question.info.quesContent))]) : t._e(), t._v(" "), 2 === t.question.info.quesContentType ? i("img", {
                                staticClass: "timu100",
                                attrs: {
                                    src: t.question.info.quesContent
                                }
                            }) : t._e()] : t._e(), t._v(" "), 1 === t.question.info.quesChoiceType ? [i("ul", {
                                    staticClass: "listIww"
                                },
                                t._l(t.question.info.quesChoice,
                                    function(e, n) {
                                        return i("li", {
                                                key: e.flag,
                                                class: [n === t.filterRight ? "cur": "", n === t.filterWrong ? "not": ""],
                                                staticStyle: {
                                                    display: "inline-flex",
                                                    width: "100%"
                                                },
                                                on: {
                                                    click: function(e) {
                                                        t.choose(n)
                                                    }
                                                }
                                            },
                                            [i("em", [t._v(t._s(e.flag) + ".")]), t._v(" "), i("span", {
                                                domProps: {
                                                    innerHTML: t._s(e.content)
                                                }
                                            })])
                                    }))] : t._e(), t._v(" "), 2 === t.question.info.quesChoiceType ? [i("ul", {
                                    staticClass: "listImage tp"
                                },
                                t._l(t.question.info.quesChoice,
                                    function(e, n) {
                                        return i("li", {
                                                key: e.flag,
                                                class: ["table", n === t.filterRight ? "cur": "", n === t.filterWrong ? "not": ""],
                                                on: {
                                                    click: function(e) {
                                                        t.choose(n)
                                                    }
                                                }
                                            },
                                            [i("em", [t._v(t._s(e.flag) + ".")]), t._v(" "), i("span", {
                                                    staticStyle: {
                                                        "background-color": "white"
                                                    }
                                                },
                                                [i("img", {
                                                    attrs: {
                                                        src: e.content
                                                    }
                                                })])])
                                    }))] : t._e()], 2)
                    },
                    staticRenderFns: []
                };
            var r = i("C7Lr")(o, a, !1,
                function(t) {
                    i("mA23")
                },
                null, null);
            e.a = r.exports
        },
        xMf3: function(t, e) {},
        yp5x: function(t, e, i) {
            "use strict";
            var n = i("ZLEe"),
                s = i.n(n),
                o = {
                    props: ["question"],
                    data: function() {
                        return {
                            findex: -1,
                            lindex: -1,
                            answerInput: [],
                            answerArr: [],
                            isRight: !1,
                            filter: 0,
                            isComplete: !1
                        }
                    },
                    computed: {
                        questionNew: function() {
                            if (this.answerArr = [], this.filter = 0, console.log(this.question.info.quesContent), 2 == this.question.info.quesContentType) for (var t in this.question.info.quesContent) if (this.question.info.quesContent[t].length > 1) for (var e in this.question.info.quesContent[t])"#" == this.question.info.quesContent[t][e] && (this.answerArr.push(t + "-" + e), -1 == this.findex && (this.findex = t), -1 == this.lindex && (this.lindex = e));
                            else "#" == this.question.info.quesContent[t] && (this.answerArr.push(t + "-0"), -1 == this.findex && (this.findex = t), -1 == this.lindex && (this.lindex = 0));
                            else if (1 == this.question.info.quesContentType || 3 == this.question.info.quesContentType) for (var t in this.question.info.quesContent)"#" == this.question.info.quesContent[t] && (this.answerArr.push(t), -1 == this.findex && (this.findex = t));
                            return this.isRight = !0,
                                this.question
                        }
                    },
                    methods: {
                        choose: function(t, e) {
                            this.findex = t,
                                this.lindex = e,
                                this.answerArr = []
                        },
                        chongP: function(t) {
                            this.findex = t,
                                this.answerArr = []
                        },
                        objKeySort: function(t) {
                            for (var e = s()(t).sort(), i = [], n = 0; n < e.length; n++) i[e[n]] = t[e[n]];
                            return i
                        },
                        changeNext: function() {
                            if (!this.isComplete) {
                                if (this.question.info.answer.length > 1) {
                                    this.isRight = !1;
                                    var t = "";
                                    for (var e in this.objKeySort(this.answerInput)) t += this.answerInput[e] + "#";
                                    if (t = t.substring(0, t.length - 1), "string" == typeof this.question.info.answer[0]) t == this.question.info.answer.join("#") && (this.isRight = !0);
                                    else for (var e in this.question.info.answer) if (t == this.question.info.answer[e].join("#")) {
                                        this.isRight = !0;
                                        break
                                    }
                                } else {
                                    var i = !0;
                                    for (var e in this.answerArr) if (console.log(this.answerInput[this.answerArr[e]]), console.log(this.question.info.answer[0]), this.answerInput[this.answerArr[e]] != this.question.info.answer[0]) {
                                        i = !1;
                                        break
                                    }
                                    this.isRight = i
                                }
                                this.isRight ? (this.filter = 1, this.$emit("next", this.question.id, 1)) : (this.filter = 2, this.$emit("next", this.question.id, 0)),
                                    this.isComplete = !0;
                                var n = this;
                                setTimeout(function() {
                                        n.findex = -1,
                                            n.lindex = -1,
                                            n.answerInput = [],
                                            n.answerArr = [],
                                            n.isRight = !1,
                                            n.isComplete = !1
                                    },
                                    1100)
                            }
                        },
                        tijiao: function(t) {
                            if (this.isComplete) return ! 1; - 1 != t ? (console.log(this.answerInput), 1 == this.question.info.quesContentType || 3 == this.question.info.quesContentType ? void 0 !== this.answerInput[this.findex] ? this.$set(this.answerInput, this.findex, this.answerInput[this.findex].toString() + t.toString()) : this.$set(this.answerInput, this.findex, t) : (console.log("xx"), console.log(this.answerInput[this.findex + "-" + this.lindex]), void 0 !== this.answerInput[this.findex + "-" + this.lindex] ? this.$set(this.answerInput, this.findex + "-" + this.lindex, this.answerInput[this.findex + "-" + this.lindex].toString() + t.toString()) : this.$set(this.answerInput, this.findex + "-" + this.lindex, t))) : 1 == this.question.info.quesContentType || 3 == this.question.info.quesContentType ? this.$set(this.answerInput, this.findex, "") : this.$set(this.answerInput, this.findex + "-" + this.lindex, "")
                        }
                    }
                },
                a = {
                    render: function() {
                        var t = this,
                            e = t.$createElement,
                            i = t._self._c || e;
                        return i("div", [i("div", {
                                staticClass: "timu",
                                staticStyle: {
                                    display: "block"
                                }
                            },
                            ["" != t.questionNew.info.quesName ? i("p", {
                                    staticClass: "fenx",
                                    staticStyle: {
                                        "text-align": "left"
                                    }
                                },
                                [t._v(t._s(t.questionNew.info.quesName))]) : t._e(), t._v(" "), 1 == t.questionNew.info.quesContentType ? i("div", {
                                    staticClass: "fenx"
                                },
                                t._l(t.questionNew.info.quesContent,
                                    function(e, n) {
                                        return i("span", {
                                                key: n,
                                                staticClass: "fend"
                                            },
                                            ["#" != e ? i("label", [t._v(t._s(e))]) : t._e(), t._v(" "), "#" == e ? i("label", [i("label", {
                                                    class: ["fkong", n == t.findex ? "item": ""],
                                                    on: {
                                                        click: function(e) {
                                                            t.chongP(n)
                                                        }
                                                    }
                                                },
                                                [t._v(t._s(t.answerInput[n]))])]) : t._e()])
                                    })) : t._e(), t._v(" "), 2 == t.questionNew.info.quesContentType ? i("div", {
                                    staticClass: "fenx"
                                },
                                t._l(t.questionNew.info.quesContent,
                                    function(e, n) {
                                        return i("span", {
                                                key: n,
                                                staticClass: "fens"
                                            },
                                            ["string" == typeof e ? ["#" != e ? [t._v(t._s(e))] : t._e(), t._v(" "), "#" == e ? i("span", {
                                                    key: n,
                                                    class: ["fkong", t.findex == n && 0 == t.lindex ? "item": ""],
                                                    on: {
                                                        click: function(e) {
                                                            t.choose(n, 0)
                                                        }
                                                    }
                                                },
                                                [t._v(t._s(t.answerInput[n + "-0"]))]) : t._e()] : t._e(), t._v(" "), t._l(e,
                                                function(s, o) {
                                                    return e.length > 1 && "string" != typeof e ? ["#" == s ? i("label", {
                                                            key: o,
                                                            class: ["fkong", t.findex == n && t.lindex == o ? "item": ""],
                                                            on: {
                                                                click: function(e) {
                                                                    t.choose(n, o)
                                                                }
                                                            }
                                                        },
                                                        [t._v(t._s(t.answerInput[n + "-" + o]))]) : t._e(), t._v(" "), "#" != s && "/" != s ? i("label", {
                                                            key: o
                                                        },
                                                        [t._v(t._s(s))]) : t._e()] : t._e()
                                                })], 2)
                                    })) : t._e(), t._v(" "), 3 == t.question.info.quesContentType ? i("div", {
                                    staticClass: "fenx"
                                },
                                t._l(t.questionNew.info.quesContent,
                                    function(e, n) {
                                        return i("span", {
                                                key: n,
                                                staticClass: "fend"
                                            },
                                            ["#" != e && "|" != e ? i("label", [t._v(t._s(e))]) : t._e(), t._v(" "), "|" == e ? i("label", [t._v("······")]) : t._e(), t._v(" "), "#" == e ? i("label", [i("label", {
                                                    class: ["fkong", n == t.findex ? "item": ""],
                                                    on: {
                                                        click: function(e) {
                                                            t.chongP(n)
                                                        }
                                                    }
                                                },
                                                [t._v(t._s(t.answerInput[n]))])]) : t._e()])
                                    })) : t._e(), t._v(" "), 1 == t.filter ? i("div", {
                                    staticClass: "panduan"
                                },
                                [i("i", {
                                    staticClass: "icon-correct04"
                                })]) : t._e(), t._v(" "), 2 == t.filter ? i("div", {
                                    staticClass: "panduan"
                                },
                                [i("i", {
                                    staticClass: "icon-error02"
                                })]) : t._e()]), t._v(" "), i("div", {
                                staticClass: "keyboard",
                                staticStyle: {
                                    display: "block"
                                }
                            },
                            [i("table", {
                                    attrs: {
                                        border: "1",
                                        cellspacing: "0",
                                        cellpadding: "0"
                                    }
                                },
                                [i("tr", [i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(1)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("1")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(2)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("2")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(3)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("3")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao( - 1)
                                            }
                                        }
                                    },
                                    [i("a", {
                                            staticClass: "redFont"
                                        },
                                        [t._v("删除")])])]), t._v(" "), i("tr", [i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(4)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("4")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(5)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("5")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(6)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("6")])]), t._v(" "), i("td", {
                                        staticClass: "dd",
                                        attrs: {
                                            rowspan: "3",
                                            valign: "middle"
                                        },
                                        on: {
                                            click: t.changeNext
                                        }
                                    },
                                    [i("span", [t._v("下一题")])])]), t._v(" "), i("tr", [i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(7)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("7")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(8)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("8")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(9)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("9")])])]), t._v(" "), i("tr", [i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(0)
                                            }
                                        }
                                    },
                                    [i("a", [t._v("0")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao(".")
                                            }
                                        }
                                    },
                                    [i("a", [t._v(".")])]), t._v(" "), i("td", {
                                        on: {
                                            click: function(e) {
                                                t.tijiao("00")
                                            }
                                        }
                                    },
                                    [i("a", [t._v("00")])])])])])])
                    },
                    staticRenderFns: []
                };
            var r = i("C7Lr")(o, a, !1,
                function(t) {
                    i("nt/v")
                },
                null, null);
            e.a = r.exports
        }
    },
    ["NHnr"]);