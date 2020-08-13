//作业展示页面
var data=[];
var useranswer="";

//使用全局变量issubmit
function setQuestionContent(data){
	var questionhtml="";
	if(data.parent.question.typeid=="1"||data.parent.question.typeid=="0"){
		if(data.type==1){
			if(data.children==''||data.children==null){
				questionhtml=questionhtml+'<div class="lanren" id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+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" height="40px;"></span><a class="edi-sy audio-btn" id="audio-btn" quesid="'+data.parent.question.id+'" type="0"><img class="sy-click" id="sy-click" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjFENzE4RTc3NTc4RjExRTY4NDFCQkQyNTY2QzI3MTgxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjFENzE4RTc4NTc4RjExRTY4NDFCQkQyNTY2QzI3MTgxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MUQ3MThFNzU1NzhGMTFFNjg0MUJCRDI1NjZDMjcxODEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MUQ3MThFNzY1NzhGMTFFNjg0MUJCRDI1NjZDMjcxODEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4j7YSZAAANtElEQVR42uxdC3BU1Rn+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" height="40px;"></a><div class="loading" style="display:none;"></div><span class="sy-right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+VX2HOwPMMMwwoiju4q4ID/JglER90eASo0aNS0zURBMTY1TiBiYajfrig4maGF98wbjHREEUUFBUdBREVByWYWSblVnvMLf/sg59yq6ue05V/X379nbPSU667719/62+OvWdr05VG+ccqKnNill9BGoKWDU1BayamgJWbcZsYdh/vOSqy9NfGe+PZXA3v/VL+mTVpjbC4jG2eD/X+2n6SNWmHbAI1ld6/4D3Z1KkVVObLkoQ2TneL/P+Mu93ev+X9+44byKhJ4Y6YlfpiUZYzjZSlMXXsyZ8P4veL6DOs12bVwHLWRMlXM2E72eH93d7x7D6UlAVRAGbsWngrpu9P837470/Qfm0AjYXZaflfjZErqaAZSNriGSTrqRx0atW9ShgRZCYBLjzRnfU5rRRJw1YjbAK2GqgTANIrEZWBewsqANcx1GFYA6teqYrmk1CkHe8LzMRdSQgSWaubBy9CzNXJgKrUgKNsI99/hne30mvNlEKRg0S1FWf6/08qJOppjUBVJsQYBE0b/D+be9vpAgdgNGMGCR4bS/0/ik617ZKetIkCZjaeqQEEYjOoffbkuHajhgkeDysC7jY+ynv13p/qCLCWhjzRAZTfPP/56DFN5NPuhzXMGuUgG2CXo3t6cSbayKsm6BSgMU3WBe8RdWK6QFsCspGeD+qrB+G4KSToAN4fVgP/H7o1QdvUXhNnhKkYEinZdcqqrgWnxsAtx+uTXRda1kji+e40Ps7vN/k/e/eDynEJgtYxwy/a5mNu5agNTCow6Kf7f0l9Ped3g+uYQTeTDRmC2jxzVQA1hRAWwRCy8WL8XHdEGDHqPdi75+jnz/j/bewtisiVFabMkpgExANG2nxOGd6P8P7fu+PZhrfJsN8DtQm+V88x1nEr7eMAUhugjxaAVsRSVL+6DygBmbDBM6IQ/Vrifd9l4ZrxzR+rk4Bh99QqH1/8jmXdLBxRTyNrFOadBkh8UKgPsX7K7zf4P0uQT3A2as3Q28py93ebyWAc41vGSAYOs976efvQF76SiP0WuilJrlXQ513rRM+jnKNK9mcDsBGjTpw05E1yWsALh4TZ6a+7P3j3r+WGe7xf09mhtAQtbsCYM8nwONxrvH+oEBP0vqCRYrwx70fhdHJcSlF2kYjyDgSvvTZhHPj+7+M8dwTi7DcTS8xYEilLIxyYcUqvloCv02G7C4BuSsAOj0HN9QuEi1AwJ0Wnd8lHcskIwPqpW/yfpv360YoP5mEd+PU8pX0t097vxHGswQez/0CSjbx/j87hmRz4oDFm34RPXBHN30LE41cEl24YRjlnidTL98LvanWmJt2ifsagceCEAUdQ1FyETbc10Ug6KVSVVoYTpmEj6tfCOfD+95KP48j4Yuvayv0Jy82zwu3toWb3kxD59nRTRsGICAM6yHSXEjAxyqvxzHXcAw/60ER66dsAuUBs8BIYlaQ3aTrPJ0iM6oHG4TnwlWlxbaBePjz6NlARu6bZDI2VxJbzcxUOoNlmAaQQNLQ/z4fg5P3V3t/IhOdTiAAMMISaLnzbaTI+HoCimEALUlKVrg+qTG5qrTUthGt+CSNRJZRMybJGR3MoR5cC9iGiVQNE0kdoyQ4imabmM+4KFFbjM7Rgf5yl/AZLCr5ECVxl0aKgGFoQRpZpcaT5DKb8nDmMxidL6ZO9CwYXJ5jmAjnJgjcudGEF1reeC7CNsLw5yipamjoX2Ki2xl0LTjku0gei4fV0yiqbSSKklICkwDTFYZiUzlUG0EKC1Vki+Qu6cytgZpIUh06TjOkJNXMIyWoAayt5LsgJF1Blmoo2VpOer4jEJ5J17OXXjckyYxhIlcMEFchNQFz7tI9uYj3YofZTxSmET4PwOixLUBjiPKgXHev9ztIfpOSPmlauwNzOOtmK6KqSwDBUYI0wZA0Wis0UJjzR+nnbTTMppqmg/x6Lccka9KQaDJJGjeihOu7nF6twIO50su2e44h0F5O1AcpkLTlEo5K5xPVWg3lm6sIazJUwFZITDVcOLw+iRKdp3v/PPQmFFKpyjLHsAlYbabRnKCZliLsAoHo7d53e/9z0jm4ztwBWT8uPfNtBMTtAiDx2DiT+D7v3/J+jY+8XeF52/UEWJdk/Lkh0IA8b89FIAt8scqjpIs2zGe5SQBXkI5MRScqjUIG+iWDW5hOm15H2J922AL00v8gf34dOS4b+jWdM8hrx0sjSKaAaKqncBcqGosb8tNo1Qg8sqTVcuWD3Gdd0iFsRjoqDfHpiFCKQE0FZeLAlSaCTQYwlqFQUKA/JygfCLOPSB1eQ3+/Omm7nKUFRCdmPcJKD47TOl1heM1FQQMrBXcLK8sZcxGV20RDogsxxWijknDncgKVqim/xKh4AX3mTgJgk94LzQLG5z1GryGa4nTzW+j9fypUkvC3p3p/D73HAqJ/TnOSVsNhS5tT5KZmG4GvQmGYzkVuUzhnzerd1Wyt5ATpLn21FWA1BNaP0c9fhV7VWnq+TaQcOIqCHDfdFFGC0zPXmp4fc4dL6OdfQm/LfzfLEbYRuBoXBYHhcmlEK53PFgBdAnyXAVNO3nkMWGkZYOYecjULhlFXcskd/m4HRTm0c2CwYD28fw70vvQE+f03vO/KKCJWSpwTnTfWkxej9zOvw+Z4J2SGQ46PNoLqUDOdWaInEphc5h7C+3Td1xIzSnB0RZKPuDreJhraO9FxO9H/S/W8WK+A09pHiZ/uYu7ZMLyVU3bOpGh8EFbWH8+8DmuSoa0kfbkKsBtGqzUVKkUpsjVMogiM7MWpB2Hd1xXQrwvIUR/u+BIfTycQzqPs/iICjgN518VwXATWKehPqECGpjmQ6zwQrK/y/hHqBDO3/qwE2C6UdwN0BQkpbcA2m29w0Ted4YJKQOcsrPvaCivLAE2GEkgqQpMMz+GaMIJeCr1JgQ8SeDsJdQCGTmDmfjICbm7RZ64o6QyK1O8iHbcDZf185iKsKUSaUoSFjB7ZMA0kRXnIADLmjR3Iz2xBBuy5RKoUcbn7bBjAb6WkaDv0axAsw50NrJwxbDKduWEoV/q8j5K6ECYlzCyBtVYlkGSpTkUEk5IfgPzKBVfQQiX+azLRSuqIpQIVqciHoxq5kkcpYMSSm8tw8kZ4RjYT/bnnuMyA2cCMVHXVALYRIm0jNFjugXUEiaqTicI1CZ/J6LUuM2TX7BPGqSLAqCfpdTmQv7BEmjAxGV5smc5qKjpEfA6UvbZBf8KhgfqqtZmgBA5WLgORAF1DB6RhvwF5VsxURPu0A3FRzAjDpTQkOqYDcCDPjRyu8MxA0Gq5ANAIHVGqBbbMcc8i0CItOFxQT2ZW1ipV0RtBr0xlHem4RtB+ucYcZtgyAp+zmWSOAzxAvlZBin5cJZthwCslbxINKu26w/HeBejP7oV1dacoqTPQX8E805SgZshwFdpnTuJyNEw1DFeTIgdkZBlpQoFLWHIjCGSiMDb0cWrkJaZDGaGzhJUVixGAJD4ugRTtED2zQwKtMAwt4XjwHu9/oJ/3TDuPHWbFQS6yNAVOZzNgfhh6xdF7KJuVhl3HaK05vljDJ3PapVSx9oj3PxH/vpOJhl3m+gNQUUvdCPm1Zk6gZqFz/wJ6M1NXw2BlWK5CrKFO9ih1MvzsPd6/CYO758wsYLuQny7lEpLaSv4UUL/3/lHvDxB4gaEGUkWYq5C9JAkq5enhevbT3/cxHRHtgPcfQK+07z4mQlvm2eDzxP0dcI+Am6FXGnhupVSWSli4EmE3AZBLnroMcA/R+bGzhNkypAN3VygaMwHYDpSXv6RCvqkYWjnw4tKY6yLALMDKjSmMwNVMhovmKqYaWLkMx9Jw/0MC640wOIVpIkqAnevBSOlIlYpHCCSG3i9Tx7yffr9XoFMHKQLug5Wbl9jo/IeYxJJTCJqok32fOtm9MIPfB1FbwM31+JqpVShonU3h5zTScwU0lonEUtLI6bGWuU783F1Ro25oQZVi2Qt3lrmSfr6NfnfE++0CTw1U4nrvH6YIip3i2RWacdpGR0gJCO8DlfjvLAz9wwJWKlZJs+wG5Clal9E1a+exc6WNTYailP7fFoC+XJHM5TrhARo1DPDr4uLRI6YjCKifMB2tzU7k2EGuSDrLCnDP2gZxNZQAChm/LWiYpQx+xZfGJdX4TQWlsFBXpZ8T+msq9NmGjq43VxNcCgpptO9G93aMKAVQtOSeVSqVSZ1lpm2hMlq4imG8Zlue+PcnSQ1YAnlZRlobKmX5TSaidxja4goar7SXVm2UExNP5rgnAggjdSQ9Fu7/9UW61tuFY4cl9E7QYOd7u00mcklDu83IKGkDxj0dE4sbKHr8A8qbveXqWnPUA4Cf6nTAlwCWhv7SdOiAhFYASVgWcxX9vIvpLEGxuJaOuSyMdvtJojKUrM3lDuC1M12QUQtyc/aSshAklp95/w3JO7m9BmoSngbKtbXh82Gbz1PQF9Pb7EVlVvn3+JpQZfge/c+RDNCWC8fBpS1foeP8G+Sdz+casLEOazKJTWl6kANZl6LCgYqIVUo6YlnJCf9vkqTmAVICjkK7b3Jss1tMjS3TaLNaQ7DvhPysnVKCJHJZQWLJNWQNAGxBWbAF7mgTvvtH6O3i8hBpoVneydCIGjowiSF5rjZ+W03S1VQ2VFoxZWF1e6SWtNt0e6JTpD2eSBKYVE/GyH5jFFkPQ1/gPzwkPeGy/lWZfk9tO8A6avQwm3IY+OUUlks2Svy2ZWPUqAKOouVPKfnYmXS0tNPFnTEI/Pj3WzNDak0EM+ME43oD9UIhUtwK/ZkaqSG7kN8VBg01xOPEV9uUsEk7WUvyFnaqX3n/HZ2zBmAO6jTLUI6XS35CEndqnnnkNEfYtCE3Chw2lAZK65JuIuDfBeWvkJf4J2RkrbiTHYG+rrnASVuFCQqp8+6mDrE7k0w+QAnd3cBrqmpjkLWawtB3jICIX7qxh+GY6PeRdBOkpFpbIj8Off0xnXw4EUlVuUhdU/OQy+Svps72N+C/jQWPj3Wln4BetdnDCq/JALbE4bBhcE+mn0NvAkDaS2Cp5bkQACiq49aWt1DEsonEFYqPDcjFx0aSxVrwvyCFPQT5ac79NJrMfbY+dYBNGzPaMj2lBCcJWLuoMTeN6NoCh/4CDcU7YLBiHs+P1VQDxceZrycaBYC6ldetNiURFjINz1UDrbbxkIveAf3thFJ5qab4eC73+lfAtsvca+UbOwLphdsjts1XC3GrR0cqG6leOhsRdsVuzUNWNg1DFUzL69XIuo4B2yYKDwXYQidgO0sGrGEWC0CexVJbB4CtqaIa5Zz6MMcKidsVUJ7FUptDwLoKACEgworT/SMErIP22+qEyY/rYc4q7xWw7aOc1PA4MfAjAivWuo5q09ywihSVgYMtgadRdR1TAltIZhAcqMneA4PLqFcbXVFn/Tr0ir5xsqCrzaeAHSWHHfV25Dg9i5tA/LVmaFepSQGb47PjMqeRVQE7MtOopjaNgB3rFuPaCdS4BGrUHFZNbWoAu6pZLDW1cQO2UcCqzRJgrT42tVkC7Ex9r5Pa+gYs0oF99D7emVpNbSzWVtbCOoEfQ6+gZJR1AmpqawLYtagTUFOrNuOcJvtq8510qakpYNXUFLBqClg1NQWsmpoCVk0Bq6amgFVTU8CqKWDV1CZv/xNgAO0EPoIk8yYbAAAAAElFTkSuQmCC" height="40px;"></span></div></div>';
			}
		}
		questionhtml=questionhtml+'<div  ';
		if(data.type==1){
			questionhtml=questionhtml+' class="title tigan" style="margin-top:10px;margin-left:10px;font-size: 1.0em;font-family: times;color:black;">';
		}else if (data.type==0){
			questionhtml=questionhtml+' class="title tigan" style="margin-top:30px;margin-left:30px;font-size: 1.0em;font-family: times;color:black;text-align:center;">';
		}
		data.parent.question.tcontent=data.parent.question.tcontent;
		questionhtml=questionhtml+data.parent.question.tcontent;
		questionhtml=questionhtml+'</div>';

		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: while;text-align:center;">';
		//选项的添加
		mui.each(data.parent.items,function(index,item){
		    questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="1" itemflag="'+item.flag+'" quesid="'+data.parent.question.id;
		    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
		    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'" wordid="'+data.parent.question.id+'">';
			questionhtml=questionhtml+'<a href="javascript:;" typeid="1" itemflag="'+item.flag+'" quesid="'+data.parent.question.id;
		    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
		    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'" wordid="'+data.parent.question.id+'">';
		    if(data.parent.question.itemtype=='1'){
		    	questionhtml=questionhtml+'<div class="mui-media-body" style="float:left;position:absolute;line-height: 100px;">';
		    }else{
		    	questionhtml=questionhtml+'<div class="mui-media-body" style="float:left;position:absolute;">';
		    }

			//正确答案和错误答案的展示
			if(issubmit=='1'){
				if(data.type==0){
					if(data.parent.answer[0].quesanswer==item.flag){
						useranswer=data.parent.answer[0].quesanswer;
						if(item.flag==data.parent.answer[0].answer){
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
						}else{
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
						}
					}else{
						if(item.flag==data.parent.answer[0].answer){
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;text-color:gray;" >';
						}else{
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block; border: 1px solid gray;" >';
						}
					}
				}else if(data.type==1){
					if(data.parent.answer[0].quesanswer==item.content){
						useranswer=item.flag;
						if(item.flag==data.parent.answer[0].answer){

							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
						}else{
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
						}
					}else{
						if(item.flag==data.parent.answer[0].answer){
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;text-color:gray;" >';
						}else{
							questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
						}
					}

				}

			}else{
				if(item.flag==data.parent.answer[0].answer){
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;text-color:gray;" >';
				}else{
					questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block; border: 1px solid gray;" >';
				}
			}

			questionhtml=questionhtml+'<span itemflag="'+item.flag+'" style="height:30px; line-height:30px; display:block; color:#666; text-align:center">';
			//选项的名称
			questionhtml=questionhtml+item.flag;
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'</div>';
			questionhtml=questionhtml+'</div>';
			//选项的内容
			if(data.parent.question.itemtype=='1'){
				questionhtml=questionhtml+'<img style="float:left;margin-left:40px;" src="'+resource+"/uploads/"+item.content+'" class="itemimg" alt="选项图片" width="120px" height="90px">';
			}else{
				questionhtml=questionhtml+'<div style="float:left;margin-left:40px;color:black;white-space: initial;text-overflow: inherit;/* overflow: hidden; */text-align: left;">'+item.content+'</div>';
			}

			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
		});
		questionhtml=questionhtml+'</ul>';
		//看是否提交数据
		if(issubmit=='1'){
			if(data.type=="0"&&type!=1){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color:black;"><font style="color: #8f8f94;">正确答案是'+useranswer;
				if(data.parent.answer[0].answer==''||data.parent.answer[0].answer==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.parent.answer[0].answer;
				}
				questionhtml=questionhtml+'</font></div>';
			}else if(data.type=="1"&&type!=1){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color:black;"><font style="color: #8f8f94;">正确答案是'+useranswer;
				if(data.parent.answer[0].answer==''||data.parent.answer[0].answer==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案"+data.parent.answer[0].answer;
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color: #8f8f94;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;margin-top: 10px;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'作答次数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			if(data.parent.answer[0].summary.erroranswer=='null'||data.parent.answer[0].summary.erroranswer==undefined||data.parent.answer[0].summary.erroranswer==""){
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">无</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer[0].summary.erroranswer+'</div>';
			}
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
				if(data.type==1){
					if((data.children==''||data.children==null)){
						//questionhtml=questionhtml+'<div class="lanren" id="lanren"><div class="play-pause audio-btn" id="audio-btn"  quesid="'+data.question.id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
						questionhtml=questionhtml+'<div class="mui-content-padded" id="listen" style="font-size:100%;color: #8f8f94;">';
						questionhtml=questionhtml+'<h5>听力材料</h5>';
						questionhtml=questionhtml+'<ul class="listen" style="text-align: left;margin-top: 10px;">';
						mui.each(data.parent.questts,function(k,v){
							if(v.flag_content==''){
								questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;">'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
						questionhtml=questionhtml+'</ul>';
						questionhtml=questionhtml+'</div>';
				    }else{
						questionhtml=questionhtml+'<div class="mui-content-padded" id="listen" style="font-size:100%;color: #8f8f94;">';
						questionhtml=questionhtml+'<h5>听力材料</h5>';
						questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
					     //听力材料获取使用ajax
						mui.each(data.parent.questts,function(k,v){
							if(v.flag_content==''){
								questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;">'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
						questionhtml=questionhtml+'</ul>';
						questionhtml=questionhtml+'</div>';
					}
				}
			}
	//如果问题是填空题的话进行填空题的样式的加载
	}else if(data.parent.question.typeid=="2"){
		if(data.type==0){
			//单词测试的展示
			questionhtml=questionhtml+'<div class="lanren" id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy audio-btn" id="audio-btn"  mp3="'+data.parent.question.mp3+'"><img class="sy-click"  id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div>';
	       var uw=data.userwords;
	       questionhtml=questionhtml+'<center style="margin-top:10px;font-size:1.2em;color:black;">'+data.parent.question.explains+'</center>';
	       questionhtml=questionhtml+'<div class="" style="margin-top:20px;"><div id="segmentedControl" class="mui-segmented-control segmentedControl" style="border: none;margin-top: 10px;text-align: center;"><ul style="padding:4px 0;margin:0;">';
	       //进行单词的展示
	        if(issubmit=='1'){
		       	mui.each(data.parent.answer.words,function(k,v){
			       	questionhtml=questionhtml+'<li style="display:inline-block;padding:0;"><a class="mui-control-item" style="width:20px;height:30px;border-bottom:1px solid;border-left:none;display:inline-block;float:left;font-family:Times New Roman;">'+v+'</li>';
			    });
	        }else{
	        	var answeruserwords=data.parent.answer.userwords;
		       	mui.each(data.parent.answer.words,function(k,v){
		       		var datav="";
		       		var ind=-1;
		       		if(data.parent.answer.userwords[k]==undefined){
		       			datav='';
		       			ind=-1;

		       		}else{

		       			datav=answeruserwords[k];
		       			ind=k;
		       		}
		       		if(k==0){
		       			questionhtml=questionhtml+'<li style="display:inline-block;padding:0;"><a class="mui-control-item mui-active" style="width:20px;height:40px;border-bottom:1px solid;border-left:none;font-family:Times New Roman;" key="'+k+'" quesid="'+data.parent.question.id+'" wordid="'+data.parent.question.wordid+'" typeid="2" homeworkid="'+data.parent.question.homeworkid+'">'+datav+'</a></li>';
		       		}else{
		       			questionhtml=questionhtml+'<li style="display:inline-block;padding:0;"><a class="mui-control-item" style="width:20px;height:40px;border-bottom:1px solid;border-left:none;font-family:Times New Roman;" key="'+k+'" quesid="'+data.parent.question.id+'" wordid="'+data.parent.question.wordid+'" typeid="2" homeworkid="'+data.parent.question.homeworkid+'">'+datav+'</a></li>';
		       		}
			    });
	        }
		    questionhtml=questionhtml+'</ul></div></div>';
             var wordtemp=0;
			//进行单词选项的展示
			questionhtml=questionhtml+'<div class="mui-content-padded" style="background:white;margin-top:30px;margin:30px;">';
			questionhtml=questionhtml+'<div class="flex-container" style="border: none;margin-top: 10px;text-align: center;"><ul style="padding:4px 0;margin:0;">';
			var items=data.parent.question.option_a.split(",");
			mui.each(items,function(key,value){
				wordtemp=wordtemp+1;
				var ind=data.parent.answer.userwords.indexOf(value);
				if(ind>-1&&key<(items.length-1)){
					data.parent.answer.userwords[ind]="-1";
					questionhtml=questionhtml+'<li style="float:left;width:20%;"><a class="worditems items'+ind+' actived" quesid="'+data.parent.question.id+'" wordid="'+data.parent.question.wordid+'" typeid="2" homeworkid="'+data.parent.question.homeworkid+'" style="font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 15px;background-color: #2bc8a0;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0;border: solid 1px transparent;color: white;" style="background-color: rgb(239, 239, 244);"><span class="mui-icon" style="font-family: inherit;"><font style="font-family: Times New Roman;" >'+value+'</font></span></a></li>';
				}else if(key<(items.length-1)){
					questionhtml=questionhtml+'<li style="float:left;;width:20%;"><a style="font-family: Muiicons;padding-left:0px;margin-left:0px;margin-top:10px;display:inline-block;padding: 0;width: 50px;height: 50px;border-radius: 15px;  background-color: #f7f7f7;text-align: center;box-shadow: 0px 3px 8px #aaa, inset 0px 2px 3px #fff;border: solid 1px transparent;color: #a7a7a7;" class="worditems items" quesid="'+data.parent.question.id+'" wordid="'+data.parent.question.wordid+'" typeid="2" homeworkid="'+data.parent.question.homeworkid+'"><span class="mui-icon" style="font-family: inherit;"><font style="font-family: Times New Roman;" >'+value+'</font></span></a></li>';
				}

			});
			questionhtml=questionhtml+'</ul></div></div>';
			//看是否提交数据
			if(issubmit=='1'){
				if(type=="0"){
					questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color: black;margin-top:'+(80+(((wordtemp-1)/5)-1)*60)+'px;"><font style="color:#8f8f94;">正确答案是<font style="font-family:Times New Roman;">'+data.parent.answer.quesanswer+"</font>";
					if(data.parent.answer.answer==''||data.parent.answer.answer==undefined){
						questionhtml=questionhtml+"，您未作答";
					}else{
						questionhtml=questionhtml+"，您的答案<font style='font-family:Times New Roman;'>";
						var datatemp=data.parent.answer.answer.split(",");
						mui.each(datatemp,function(kk,vv){
							questionhtml=questionhtml+vv;
						});
						questionhtml=questionhtml+'</font>';
					}
					questionhtml=questionhtml+'</font></div>';
				}else{
					questionhtml=questionhtml+'<br><br><br><br><br><br><br><br><br>';
				}
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color:#8f8f94;">';
				questionhtml=questionhtml+'<h5>班级数据</h5>';
				questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;margin-top:10px;">';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:100%;">';
				questionhtml=questionhtml+'作答次数';
				questionhtml=questionhtml+'</span>';
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer.summary.answernum+'</div>';
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:100%;">';
				questionhtml=questionhtml+'正确率';
				questionhtml=questionhtml+'</span>';
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer.summary.accrate+'</div>';
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:100%;">';
				questionhtml=questionhtml+'易错单词';
				questionhtml=questionhtml+'</span>';
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">';
				if(data.parent.answer.summary.erroranswer==""){
					questionhtml=questionhtml+"无";
				}else{
					questionhtml=questionhtml+"<font style='font-family:Times New Roman;'>";
					var datatemp=data.parent.answer.summary.erroranswer.split(",");
					mui.each(datatemp,function(kk,vv){
						questionhtml=questionhtml+vv;
					});
					questionhtml=questionhtml+"</font>";
				}


				questionhtml=questionhtml+'</div>';
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}else{
			//听力训练的展示
			questionhtml=questionhtml+'<div class="lanren" id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc5MUNBQzZFNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc5MUNBQzZGNTdCRTExRTY4OTQ3RjhFRkEwQThDQUIzIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzkxQ0FDNkM1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzkxQ0FDNkQ1N0JFMTFFNjg5NDdGOEVGQTBBOENBQjMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5DOUrSAAAQsklEQVR42uxdW4ykRRU+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" height="40px;"></span><a class="edi-sy audio-btn" id="audio-btn"  quesid="'+data.parent.question.id+'" type="0"><img class="sy-click"  id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><div class="loading" style="display:none;"></div><span class="sy-right"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKwAAABnCAYAAACdHqmvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0RUUyRUVCNTdCRjExRTZCREFCREM2QURGMjI2MzgyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0RUUyRUVDNTdCRjExRTZCREFCREM2QURGMjI2MzgyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzRFRTJFRTk1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzRFRTJFRUE1N0JGMTFFNkJEQUJEQzZBREYyMjYzODIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7iozswAAAQpElEQVR42uxdWaykRRU+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" height="40px;"></span></div></div>';
			//questionhtml=questionhtml+'<div class="lanren" id="lanren" style="margin-top:10px;"><div class="play-pause audio-btn" id="audio-btn"   quesid="'+data.question.id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
			questionhtml=questionhtml+'<div class="tkcontent" style="margin-top:20px;margin-left:30px;font-size: 1.0em;font-family: times;color: black;text-align:left;">';
			//进行正则表达式的替换要是要蓝色
			var patt = new RegExp("#{2}答案\\[(.*?)\\]#{2}","g");
	        var result;
	        var sortid=0;
	        var content=data.parent.question.tcontent;
	        while ((result = patt.exec(content)) != null)  {
	        	var replace='<input id="eninput" type="text" class="mui-input-clear eninput"  typeid="2"  quesid="'+data.parent.question.id;
			    replace=replace+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid+'"';
			    replace=replace+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'"';
			    replace=replace+' style="width:80px;background-color:blueviolet;border-radius:0.45em;color:white;text-align:center;height:25px;" placeholder="'+data.parent.answer[sortid].answer_num+'"';
			    if(data.parent.answer[sortid].answer!=null&&data.parent.answer[sortid].answer!=undefined){
			    	replace=replace+' value="'+data.parent.answer[sortid].answer+'"/>';
			    }else{
			    	replace=replace+' value=""/>';
			    }
	            content=content.replace(result[0], replace);
	           	sortid=sortid+1;
	        }
			questionhtml=questionhtml+content;
			questionhtml=questionhtml+'</div>';
			if(issubmit=='1'){
				if(type=="0"){
					questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;color:black;"><font style="color: #8f8f94;">正确答案是'+data.parent.answer[0].quesanswer;
					if(data.parent.answer.userans==''||data.parent.answer.userans==undefined){
						questionhtml=questionhtml+"，您未作答";
					}else{
						questionhtml=questionhtml+"，您的答案"+data.parent.answer[0].answer;
					}
					questionhtml=questionhtml+'</font></div>';
				}
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;">';
				questionhtml=questionhtml+'<h5>班级数据</h5>';
				questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;color: #8f8f94;">';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:0.7em;">';
				questionhtml=questionhtml+'作答次数';
				questionhtml=questionhtml+'</span>';
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.parent.answer[0].summary.answernum+'</div>';
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:0.7em;">';
				questionhtml=questionhtml+'正确率';
				questionhtml=questionhtml+'</span>';
				questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.parent.answer[0].summary.accrate+'</div>';
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
				questionhtml=questionhtml+'<a href="javascript:void(0);">';
				questionhtml=questionhtml+'<span style="font-size:0.7em;">';
				questionhtml=questionhtml+'易错项';
				questionhtml=questionhtml+'</span>';
				//questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
				if(data.parent.answer[0].summary.erroranswer=='null'||data.parent.answer[0].summary.erroranswer==undefined){
					questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;"></div>';
				}else{
					questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.parent.answer[0].summary.erroranswer+'</div>';
				}
				questionhtml=questionhtml+'</a>';
				questionhtml=questionhtml+'</li>';
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
				if(data.parent.length==0){
					//questionhtml=questionhtml+'<div class="lanren" id="lanren">	<div class="play-pause audio-btn" id="audio-btn" quesid="'+data.question.id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
					questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
					questionhtml=questionhtml+'<h5>听力材料</h5>';
					questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
				     //听力材料获取使用ajax
					mui.each(data.parent.questts,function(k,v){
						if(v.flag_content==''){
							questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;">'+v.tts_content+"</h5></li>";
						}else{
							questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
						}
					});
					questionhtml=questionhtml+'</ul>';
					questionhtml=questionhtml+'</div>';
				}else{
					questionhtml=questionhtml+'<div class="mui-content-padded" id="listen" style="font-size:0.7em;color: #8f8f94;">';
					questionhtml=questionhtml+'<h5>听力材料</h5>';
					questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
				     //听力材料获取使用ajax
					mui.each(data.parent.questts,function(k,v){
						if(v.flag_content==''){
							questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;">'+v.tts_content+"</h5></li>";
						}else{
							questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
						}
					});
					questionhtml=questionhtml+'</ul>';
					questionhtml=questionhtml+'</div>';
				}
			}

		}

	//如果问题是判断题的话进行判断题的样式的加载
	}else if(data.parent.question.typeid=="3"){
		var colorflag=0;
		if(data.children==''||data.children==null){
		    questionhtml=questionhtml+'<div class="lanren" id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy audio-btn" id="audio-btn" quesid="'+data.parent.question.id+'" type="0"><img class="sy-click" id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><div class="loading" style="display:none;"></div><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div>';
		}
		questionhtml=questionhtml+'<div class="title" style="margin-top:20px;margin-left:30px;font-size: 1.0em;font-family: times;color: blueviolet;">';
		data.parent.question.tcontent=data.parent.question.tcontent;
		questionhtml=questionhtml+data.parent.question.tcontent;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<ul class="mui-table-view xuanze" style="margin-top: 30px;background-color: white">';
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="3" itemflag="1" quesid="'+data.parent.question.id;
	    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'">';
		questionhtml=questionhtml+'<a href="javascript:;" typeid="3" itemflag="1" quesid="'+data.parent.question.id;
	    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body" style="">';
		if(issubmit==1)
		{
			var itemhtml="";
			if("1"==data.parent.answer[0].quesanswer){
				if(data.parent.answer[0].answer!='null'&&data.parent.answer[0].answer!='undefined'&&data.parent.answer[0].answer!=undefined){
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;" >';
				}else{
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}

			}
			if(type==1){
				if("1"==data.parent.answer[0].quesanswer){
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
			    }
			}

			if("1"==data.parent.answer[0].answer){
				itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" str=="22222">';
		    }

			if("1"!=data.parent.answer[0].answer&&"1"!=data.parent.answer[0].quesanswer){
				itemhtml='<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;" >';
			}
			questionhtml=questionhtml+itemhtml;
		}else{
			if("1"==data.parent.answer[0].answer){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
		    }else{
		    	questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
		    }
		}
		questionhtml=questionhtml+'<span itemflag="1" style="height:30px; line-height:30px; display:block; color:black; text-align:center">';
		//选项的名称
		questionhtml=questionhtml+"A";
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;<font style="color:black;">√</font>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'<li class="mui-table-view-cell mui-media" typeid="3" itemflag="0" quesid="'+data.parent.question.id;
	    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'">';
		questionhtml=questionhtml+'<a href="javascript:;" typeid="3" itemflag="0" quesid="'+data.parent.question.id;
	    questionhtml=questionhtml+'" answerid="'+data.parent.answer[0].quesansid+'" homeworkid="'+data.parent.question.homeworkid;
	    questionhtml=questionhtml+'" examsid="'+data.parent.question.examsid+'" quizid="'+data.parent.question.quizid+'">';
		questionhtml=questionhtml+'<div class="mui-media-body">';
		if(issubmit==1)
		{
			var itemhtml="";

			if("0"==data.parent.answer[0].quesanswer){
				if(data.parent.answer[0].answer!='null'&&data.parent.answer[0].answer!='undefined'&&data.parent.answer[0].answer!=undefined){
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:red; border-radius:25px;display: inline-block;" >';
				}else{
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
				}
			}

			if(type==1){
				if("0"==data.parent.answer[0].quesanswer){
					itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
			    }
			}

			if("0"==data.parent.answer[0].answer){
				itemhtml='<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
			}
			if("0"!=data.parent.answer[0].answer&&"0"!=data.parent.answer[0].quesanswer){
				itemhtml='<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
			}
			questionhtml=questionhtml+itemhtml;
		}else{
			if("0"==data.parent.answer[0].answer){
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:#2bc8a0; border-radius:25px;display: inline-block;" >';
			}else{
				questionhtml=questionhtml+'<div class="items" style="width:30px; height:30px; background-color:white; border-radius:25px;display: inline-block;border: 1px solid gray;" >';
			}
		}

		questionhtml=questionhtml+'<span itemflag="0" style="height:30px; line-height:30px; display:block; color:black; text-align:center">';
		//选项的名称
		questionhtml=questionhtml+"B";
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</div>&nbsp;&nbsp;<font style="color:black;">×</font>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'</a>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		if(issubmit=='1'){
			if(type=="0"){
				questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color:black;"><font style="color: #8f8f94;">正确答案是';
				if(data.parent.answer[0].quesanswer=='1'){
					questionhtml=questionhtml+'√';
				}else{
					questionhtml=questionhtml+'×';
				}
				if(data.parent.answer[0].answer==''||data.parent.answer[0].answer==undefined){
					questionhtml=questionhtml+"，您未作答";
				}else{
					questionhtml=questionhtml+"，您的答案";
					if(data.parent.answer[0].answer=='1'){
						questionhtml=questionhtml+"√";
					}else{
						questionhtml=questionhtml+"×";
					}
				}
				questionhtml=questionhtml+'</font></div>';
			}
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:100%;color: #8f8f94;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;margin-top: 10px;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'作答次数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">'+data.parent.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:100%;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:100%;">';
			if(data.parent.answer[0].summary.erroranswer=='1'){
				questionhtml=questionhtml+'√';
			}else if(data.parent.answer[0].summary.erroranswer=='0'){
				questionhtml=questionhtml+'×';
			}else{
				questionhtml=questionhtml+'无';
			}
			questionhtml=questionhtml+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.children==null){
				//questionhtml=questionhtml+'<div class="lanren" id="lanren">	<div class="play-pause audio-btn" id="audio-btn"  quesid="'+data.question.id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen" style="font-size:100%;color: #8f8f94;">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;  margin-top: 10px;">';
			     //听力材料获取使用ajax
				mui.each(data.parent.questts,function(k,v){
					if(v.flag_content==''){
						questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;">'+v.tts_content+"</h5></li>";
					}else{
						questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				mui.each(data.parent.questts,function(k,v){
					if(v.flag_content==''){
						questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;">'+v.tts_content+"</h5></li>";
					}else{
						questionhtml=questionhtml+'<li><h5 style="font-size:100%;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}

		//如果问题是排序题的话进行排序题的样式的加载
	}else if(data.parent.question.typeid=="4"){
		//排序题样式
		questionhtml=questionhtml+'<div class="lanren" id="lanren"><div class="play-pause audio-btn" id="audio-btn" quesid="'+data.parent.question.id+'" type="0"></div><div class="loading" style="display:none;"></div></div>';
		questionhtml=questionhtml+'<div class="title" style="margin-top:20px;margin-left:30px;font-size: 1.0em;font-family: times;color: blueviolet;">';
		questionhtml=questionhtml+data.parent.question.tcontent;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div style="padding: 10px 10px;"><div id="segmentedControl" class="mui-segmented-control segmentedControl">';
		mui.each(data.answer,function(indexa,itema){
			questionhtml=questionhtml+'<a class="mui-control-item';
			if(indexa==0){
				questionhtml=questionhtml+' mui-active" typeid="4"  quesid="'+data.question.id;
			}else{
				questionhtml=questionhtml+'" typeid="4"  quesid="'+data.question.id;
			}
	    	questionhtml=questionhtml+'" answerid="'+itema.quesansid+'" homeworkid="'+data.question.homeworkid;
	    	questionhtml=questionhtml+'" examsid="'+data.question.examsid+'" quizid="'+data.question.quizid+'">';
	    	questionhtml=questionhtml+'<label style="color: black;">'+itema.answer_num+'&nbsp;&nbsp;</label>';
			questionhtml=questionhtml+'<font color="red;">';
			if(itema.answer!=''&&itema.answer!=null&&itema.answer!=undefined){
				questionhtml=questionhtml+itema.answer;
			}else{

			}
			questionhtml=questionhtml+'</font></a>';
		});
		questionhtml=questionhtml+'</div></div>';
		questionhtml=questionhtml+'<div><div class="mui-control-content mui-active" style="height:500px;">';
		questionhtml=questionhtml+'<div id="scroll" class="mui-scroll-wrapper"><div class="mui-scroll">';
		questionhtml=questionhtml+'<ul class="mui-table-view">';
		mui.each(data.items,function(index,item){
			questionhtml=questionhtml+'<li class="mui-table-view-cell pxt"  questext="'+item.flag+'" style="background-color: #efeff4;">';
			questionhtml=questionhtml+'<span>'+item.flag+'</span>.<a href="javascript:void(0);" style="display:inline;">';
			if(data.question.itemtype=='1'){
				questionhtml=questionhtml+'<img width="120px" height="90px" src="http://en.czbanbantong.com/uploads/'+item.content+'" class="itemimg" alt="选项图片">';
			}else{
				questionhtml=questionhtml+item.content;
			}
			questionhtml=questionhtml+'</a></li>';
		});
		questionhtml=questionhtml+'</ul></div></div></div></div>';
		if(issubmit=='1'){
			//由于排序题改成了选择题所以这部分需要的话在补充
			questionhtml=questionhtml+'<div class="mui-content-padded" style="font-size:0.7em;color: #8f8f94;">';
			questionhtml=questionhtml+'<h5>班级数据</h5>';
			questionhtml=questionhtml+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'作答次数';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.answernum+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'正确率';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.accrate+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'<li class="mui-table-view-cell" style="width: 32%;">';
			questionhtml=questionhtml+'<a href="javascript:void(0);">';
			questionhtml=questionhtml+'<span style="font-size:0.7em;">';
			questionhtml=questionhtml+'易错项';
			questionhtml=questionhtml+'</span>';
			questionhtml=questionhtml+'<div class="mui-media-body" style="font-size:0.7em;">'+data.answer[0].summary.erroranswer+'</div>';
			questionhtml=questionhtml+'</a>';
			questionhtml=questionhtml+'</li>';
			questionhtml=questionhtml+'</ul>';
			questionhtml=questionhtml+'</div>';
			if(data.parent.length==0){
				//questionhtml=questionhtml+'<div class="lanren" id="lanren">	<div class="play-pause audio-btn" id="audio-btn" quesid="'+data.question.id+'" type="0"></div><div id="loading" style="display:none;"></div></div>';
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen" style="font-size:0.7em;color: #8f8f94;">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question.id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;">'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}else{
				questionhtml=questionhtml+'<div class="mui-content-padded" id="listen">';
				questionhtml=questionhtml+'<h5>听力材料</h5>';
				questionhtml=questionhtml+'<ul class="listen" style="text-align: left;">';
			     //听力材料获取使用ajax
				var questionid=data.question.id;
				mui.ajax("getQuestiontts",
					{
					data:{
						quesid:questionid,
						type:"1",
						ran:Math.random()
					},
					dataType:'json',//服务器返回json格式数据
					type:'post',//HTTP请求类型
					timeout:10000,//超时时间设置为10秒；
					async:false,
					success:function(data){
						//服务器返回响应，根据响应结果，进行听力材料的展示问题

						mui.each(data,function(k,v){
							if(v.flag_content==''){
								questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;">'+v.tts_content+"</h5></li>";
							}else{
								questionhtml=questionhtml+'<li><h5 style="font-size:20px;color: #8f8f94;"><strong>'+v.flag_content+'</strong>:'+v.tts_content+'</h5><li>';
							}
						});
					},
					error:function(xhr,type,errorThrown){
						//异常处理；
						console.log("错误");
						return errorThrown;
					}
				});
				questionhtml=questionhtml+'</ul>';
				questionhtml=questionhtml+'</div>';
			}
		}
	}
	return questionhtml;
}
//进行题型的类的封装其中tts表示的是音频，type表示的是题型其中题干的类型是0，stem表示的是题干的内容
function setContent(data){
	var questionhtml="";
	//如果问题是组合试题
	if(data.children!=''&&data.children!=null){
		document.getElementById("quesnum").style.display="";
		questionhtml=questionhtml+'<div  id="parent" class="parent" stemid="'+data.children.id+'"  style="margin-left: 0px;">';
		questionhtml=questionhtml+'<div class="" style="margin-top:0px;margin-left:10px;font-family: times;text-align:left;font-size: 14px;color: #8f8f94;">';
		questionhtml=questionhtml+data.children.content;
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div class="lanren" id="lanren" style="text-align:center;"><div style="width:98%;margin-left:auto;margin-right:auto;padding-top:10px;"><span class="sy-left"><img src="../../public/Homework/images/sy_left.png" height="40px;"></span><a class="edi-sy audio-btn" id="audio-btn"  quesid="'+data.parent.question.id+'" type="1"><img class="sy-click" id="sy-click" src="../../public/Homework/images/sy.png" height="40px;"></a><div class="loading" style="display:none;"></div><span class="sy-right"><img src="../../public/Homework/images/sy_right.png" height="40px;"></span></div></div>';
        questionhtml=questionhtml+'<div style="margin-top:10px;margin-left:10px;font-family: times;text-align:left;font-size:0.8em;color: #8f8f94;" class="tigan">';
		questionhtml=questionhtml+data.parent.question.stemcontent;
		questionhtml=questionhtml+'</div>';
	    questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
	}else if(data.source=='1'&&data.parent.length>0){
		questionhtml=questionhtml+'<div id="childrentitle">';
		questionhtml=questionhtml+'<ul class=" childrenques mui-table-view mui-table-view-striped mui-table-view-condensed" style="background:#EEEEEE;">';
		questionhtml=questionhtml+'<li class="mui-table-view-cell">';
		questionhtml=questionhtml+'<h4>';
		questionhtml=questionhtml+'<span class="mui-pull-right" id="quesnum">';
		questionhtml=questionhtml+'<strong style="color:red;font-size:1.5em;" id="cquesindex">1</strong>/'+qunum;
		questionhtml=questionhtml+'</span>';
		questionhtml=questionhtml+'</h4>';
		questionhtml=questionhtml+'</li>';
		questionhtml=questionhtml+'</ul>';
		questionhtml=questionhtml+'</div>';
		questionhtml=questionhtml+'<div id="citem" class="mui-control-content mui-active" style="overflow: scroll;">';
		questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
	}else{
		document.getElementById("quesnum").style.display="";
		try{
			if(data.parent.question.stemcontent){
				questionhtml=questionhtml+"<div style='margin-top: 0px;margin-left:0px;'>";
				questionhtml=questionhtml+data.parent.question.stemcontent;
				questionhtml=questionhtml+"</div>";
			}
		}catch(e){

		}
		questionhtml=questionhtml+'<div id="parent" class="parent" quescount="'+data.parent.question.quescount+'" stemid="'+data.parent.question.stemid+'" style="height:100%;margin-left: 0px;">';
		questionhtml=questionhtml+setQuestionContent(data);
		questionhtml=questionhtml+'</div>';
	}
	return questionhtml;
}
//请求响应
function getRespose(url,errinfo,index,source,stemid,pageindex,isasync){
	var respose="";
	listendata=[];
	mui.ajax(url,
		{
		data:{
			studentId:studentid,
			classId:classid,
			homeworkid:homeworkid,
			index:index,
			type:source,
			examid:examid,
			wtcount:wtcount,
			wacount:wacount,
			tacount:tacount,
			iserror:iserror,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:4000,//超时时间设置为10秒；
		async:isasync,
		success:function(data){
			//服务器返回响应，根据响应结果，将试题进行赋值
			pagedata[pageindex]=data;
			var obj={};
			if(data.children==null||data.children=='null'||data.children==''||data.children==undefined){
				obj.type=0;
			}else{
				obj.type=1;
			}
			if(data.type==1){
				obj.stemid=data.parent.question.stemid;
			}else{
				obj.stemid='0';
			}
			page_obj[pageindex]=obj;

			//类型的展示
			if(data.type==0){
				document.getElementById("type").innerHTML="单词测试";
			}else if(data.type==1){
				document.getElementById("type").innerHTML="听力训练";
				page_tts[pageindex]=data.parent.questts;
			}else if(data.type==2){
				document.getElementById("type").innerHTML="单词朗读";
			}else if(data.type==3){
				document.getElementById("type").innerHTML="课文朗读";
			}
			if(data.type==0||data.type==1){
				content=setContent(data);
			}else if(data.type==2||data.type==3){
				content=aloundContent(data[0]);
			}
			var muiindex="item"+pageindex;
			document.getElementById(muiindex).innerHTML=content;
			page[pageindex]="1";

			//进行题干图片的压缩
			var screenwidth=window.screen.availWidth;
			var objs=document.getElementsByClassName("tigan");
			mui.each(objs,function(){
				try{
					var imgs=this.getElementsByTagName("img");
					mui.each(imgs,function(key,value){
							var currimg=this;
							var image = new Image();
							image.src = this.src;
							var naturalWidth=0;
							image.onload = function(){
								var _stemp = this;
								naturalWidth=_stemp.width;
								console.log(screenwidth);
								if(naturalWidth>screenwidth){
										currimg.style.width=(screenwidth)-10+"px";
								}
							}
					});
				}catch(e){
				}
			});


			//处理题干中图片的问题
//			try{
//				//计算图片的宽高比例
//				var imgrate=document.getElementById(muiindex).getElementsByClassName("tigan")[0].getElementsByTagName("img")[0].style.height/document.getElementById(muiindex).getElementsByClassName("tigan")[0].getElementsByTagName("img")[0].style.width;
//				document.getElementById(muiindex).getElementsByClassName("tigan")[0].getElementsByTagName("img")[0].style.width=(document.body.availWidth-10)+"px";
//				document.getElementById(muiindex).getElementsByClassName("tigan")[0].getElementsByTagName("img")[0].style.height=(document.body.availWidth-10)*imgrate+"px";
//			}catch(e){
//
//			}

			//题干的图片的展示问题
			if(issubmit==0&&isOverdue=='false'){
				if(data.type==1){
						//进行单击事件的注入选择试题和判断试题的js注入
						if(data.parent.question.typeid=='1'||data.parent.question.typeid=='3'){
							mui('#item'+pageindex+' ul.xuanze li').on('click', 'a', function() {

								//回答数据进行异步请求
								var items=this.parentNode.parentNode.getElementsByTagName("li");
								for(var i=0;i<items.length;i++){
									items[i].getElementsByTagName("div")[1].style.background="white";
								}
								this.getElementsByTagName("div")[1].style.background="#2bc8a0";
								var questionid=this.getAttribute("quesid");
								var homeworkid=this.getAttribute("homeworkid");
								var quizid=this.getAttribute("quizid");
								var examsid=this.getAttribute("examsid");
								var answerid=this.getAttribute("answerid");
								var useranswer=this.getAttribute("itemflag");
								var typeid=this.getAttribute("typeid");
								//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
								var url="setUseranswer";
								mui.ajax(url,
									{
									data:{
										questionid:questionid,
										useranswer:useranswer,
										homeworkid:homeworkid,
										quizid:quizid,
										answerid:answerid,
										examsid:examsid,
										typeid:typeid,
										ran:Math.random()
									},
									dataType:'json',//服务器返回json格式数据
									type:'post',//HTTP请求类型
									timeout:10000,//超时时间设置为10秒；
									async:true,
									success:function(data){
										//服务器返回然后进行h5的本地存储
										try{
											websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
										}catch(e){
											console.log("不支持websql");
										}
										//用户回答问题之后直接进行跳转
										var next=document.getElementById("next");
										mui.trigger(next,'click');
									},
									error:function(xhr,type,errorThrown){
										//异常处理；
										return errinfo;
									}
								});
				      	    });
						}
						//填空试题的js进行注入事件
						if(data.parent.question.typeid=='2'){
							mui("div.title").on('input',"input.eninput",function(e){
								var questionid=this.getAttribute("quesid");
								var homeworkid=this.getAttribute("homeworkid");
								var quizid=this.getAttribute("quizid");
								var examsid=this.getAttribute("examsid");
								var answerid=this.getAttribute("answerid");
								var useranswer=this.value;
								var typeid=this.getAttribute("typeid");
								//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
								var url="setUseranswer";
								mui.ajax(url,
									{
									data:{
										questionid:questionid,
										useranswer:useranswer,
										homeworkid:homeworkid,
										quizid:quizid,
										answerid:answerid,
										examsid:examsid,
										typeid:typeid,
										ran:Math.random()
									},
									dataType:'json',//服务器返回json格式数据
									type:'post',//HTTP请求类型
									timeout:10000,//超时时间设置为10秒；
									async:true,
									success:function(data){
										//服务器返回然后进行h5的本地存储
										try{
											websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
										}catch(e){
											console.log("不支持websql");
										}
									},
									error:function(xhr,type,errorThrown){
										//异常处理；
										return errinfo;
									}
								});
							});
						}

						//排序试题的js进行注入事件
						if(data.parent.question.typeid=='4'){
							mui("div#scroll").on('tap','li',function(){
								var ques=this.getAttribute("questext");
								//选择active的元素
								document.getElementsByClassName("mui-control-item mui-active")[0].getElementsByTagName("font")[0].innerHTML=ques;
								//将用户的答案进行写入数据库
								if(document.getElementsByClassName("mui-control-item mui-active").length>0){
									var url="setUseranswer";
									var questionid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("quesid");
									var homeworkid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("homeworkid");
									var quizid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("quizid");
									var examsid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("examsid");
									var answerid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("answerid");
									var useranswer=ques;
									var typeid=document.getElementsByClassName("mui-control-item mui-active")[0].getAttribute("typeid");
									mui.ajax(url,
										{
										data:{
											questionid:questionid,
											useranswer:useranswer,
											homeworkid:homeworkid,
											quizid:quizid,
											answerid:answerid,
											examsid:examsid,
											typeid:typeid,
											ran:Math.random()
										},
										dataType:'json',//服务器返回json格式数据
										type:'post',//HTTP请求类型
										timeout:10000,//超时时间设置为10秒；
										async:true,
										success:function(data){
											//服务器返回然后进行h5的本地存储
											try{
												websqlInsterDataToTable("questions",(index+1),answerid,questionid,homeworkid,examsid,studentid,classid,"0");
											}catch(e){
												console.log("不支持websql");
											}
										},
										error:function(xhr,type,errorThrown){
											//异常处理；
											return errinfo;
										}
									});
								}
								var items=document.getElementsByClassName("mui-control-item");
								for(var i=0;i<items.length;i++){
									if(hasClass(items[i],"mui-active")&&i<items.length-1){
										removeClass(items[i],"mui-active");
										addClass(items[i+1],"mui-active");
										i=items.length;
									}else{
										if(i==(items.length-1)){
											removeClass(items[i],"mui-active");
										}
									}
								}
							});
						}
					}
					if(data.type==0){
						//进行单击事件的注入选择试题和判断试题的js注入
						if(data.parent.question.typeid=='1'||data.parent.question.typeid=='0'){
							mui('#item'+pageindex+' ul.xuanze li').on('tap', 'a', function() {
								var ind=parseInt(document.getElementById("quesindex").innerHTML);
								var items=this.parentNode.parentNode.getElementsByTagName("li");
								for(var i=0;i<items.length;i++){
									items[i].getElementsByTagName("div")[1].style.background="white";
								}
								this.getElementsByTagName("div")[1].style.background="#2bc8a0";
								var questionid=this.getAttribute("quesid");
								var homeworkid=this.getAttribute("homeworkid");
								var wordid=this.getAttribute("wordid");
								var useranswer=this.getAttribute("itemflag");
								var typeid=this.getAttribute("type");
								//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
								var url="setUserWordtestanswer";
								mui.ajax(url,
									{
									data:{
										questionid:questionid,
										useranswer:useranswer,
										studentid:studentid,
										classid:classid,
										homeworkid:homeworkid,
										wordid:wordid,
										typeid:typeid,
										ran:Math.random()
									},
									dataType:'json',//服务器返回json格式数据
									type:'post',//HTTP请求类型
									timeout:10000,//超时时间设置为10秒；
									async:true,
									success:function(data){
										//服务器返回然后进行h5的本地存储
										try{
											websqlInsterDataToTable("wordtest",(parseInt(ind)-1),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
										}catch(e){
											console.log("不支持websql");
										}
										var next=document.getElementById("next");
										mui.trigger(next,'click');
									},
									error:function(xhr,type,errorThrown){
										//异常处理；
										console.log(errorThrown);
										return errorThrown;
									}
								});
				      	    });
						}
						//填空试题的js进行注入事件
						if(data.parent.question.typeid=='2'){
							mui('#item'+pageindex+' div.flex-container').on('tap',"li",function(e){
								var obj=this.getElementsByClassName("worditems")[0];
								var ind=parseInt(document.getElementById("quesindex").innerHTML);
								var text=obj.getElementsByTagName("span")[0].getElementsByTagName("font")[0].innerHTML;
								//alert(document.getElementsByClassName("mui-active")[0].innerHTML=="");
								if(obj.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("mui-active").length>0&&!hasClass(obj,"actived")&&this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("mui-active")[0].innerHTML==""){
									obj.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("mui-active")[0].innerHTML=text;
									obj.style.backgroundColor="#2bc8a0";
									obj.style.color="#2bc8a0";
									obj.style.boxShadow="0px 3px 8px #aaa, inset 0px 2px 3px #2bc8a0";
									//obj.parentElement.style.backgroundColor="#ddd";
									obj.parentElement.getElementsByTagName("font")[0].style.color="white";

									addClass(obj,"actived");
									//向下移动
									var items=obj.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("mui-control-item");
									for(var i=0;i<items.length;i++){
										if(hasClass(items[i],"mui-active")&&i<items.length-1){
											removeClass(items[i],"mui-active");
											addClass(items[i+1],"mui-active");
											addClass(obj,"items"+i);
											i=items.length;
										}else{
											if(i==(items.length-1)){
												removeClass(items[i],"mui-active");
												addClass(obj,"items"+i);
											}
										}
									}
									var questionid=obj.getAttribute("quesid");
									var wordid=obj.getAttribute("wordid");
									var homeworkid=obj.getAttribute("homeworkid");
									var typeid=obj.getAttribute("typeid");
									//答案
									var userans=obj.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("segmentedControl")[0].getElementsByTagName("a");
									var ans="";
									var allword=true;
									for(var i=0;i<userans.length;i++){
										var value=userans[i].innerText;
										if(value==""){
											allword=false;
										}
										if(i<(userans.length-1)){
											ans=ans+value+",";
										}else{
											ans=ans+value;
										}
									}

									//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
									var url="setUserWordtestanswer";
									mui.ajax(url,
										{
										data:{
											questionid:questionid,
											useranswer:ans,
											studentid:studentid,
											classid:classid,
											homeworkid:homeworkid,
											wordid:wordid,
											typeid:typeid,
											ran:Math.random()
										},
										dataType:'json',//服务器返回json格式数据
										type:'post',//HTTP请求类型
										timeout:10000,//超时时间设置为10秒；
										async:true,
										success:function(data){
											//服务器返回然后进行h5的本地存储
											try{
												websqlInsterDataToTable("wordtest",(ind),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
											}catch(e){
												console.log("不支持websql");
											}
											if(allword){
												//用户回答问题之后直接进行跳转
												var next=document.getElementById("next");
												mui.trigger(next,'click');
											}

										},
										error:function(xhr,type,errorThrown){
											//异常处理；
											return errorThrown;
										}
									});
								}
							});

							mui('#item'+pageindex+' .segmentedControl').on("tap", "a", function() {
								try{
									var index=pageindex;
								var ind=parseInt(document.getElementById("quesindex").innerHTML);
								var key=this.getAttribute("key");
								var classname="items"+key;
								if(key!='-1'){
									this.innerHTML="";
									//this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].style.backgroundColor="";

									//改变颜色
									console.log(classname);
									console.log(this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0]);
									this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].style.backgroundColor="#f7f7f7";
									this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].style.color="#a7a7a7";
									this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].style.boxShadow="0px 3px 8px #aaa, inset 0px 2px 3px #fff";
									this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].getElementsByTagName("font")[0].style.color="";
									//this.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].parentElement.style.backgroundColor="#a7a7a7;";
									//this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].parentElement.style.backgroundColor="#8F8F94";
									//this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0].getElementsByTagName("font")[0].style.backgroundColor="#8F8F94";
									removeClass(this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0],"actived");
									removeClass(this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("flex-container")[0].getElementsByClassName(classname)[0],classname);
									//document.getElementById(key).setAttribute("id",'-1');
									var questionid=this.getAttribute("quesid");
									var wordid=this.getAttribute("wordid");
									var homeworkid=this.getAttribute("homeworkid");
									var typeid=this.getAttribute("typeid");
									//答案
									var userans=this.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("segmentedControl")[0].getElementsByTagName("a");
									var ans="";
									for(var i=0;i<userans.length;i++){
										var value=userans[i].innerText;
										if(i<(userans.length-1)){
											ans=ans+value+",";
										}else{
											ans=ans+value;
										}
									}
									//将用户的答案直接进行后台的存储在此进行性html中localstorage进行存储进行结果汇总的时候不行行页面的刷新
									var url="setUserWordtestanswer";
									mui.ajax(url,
										{
										data:{
											questionid:questionid,
											useranswer:ans,
											studentid:studentid,
											classid:classid,
											homeworkid:homeworkid,
											wordid:wordid,
											typeid:typeid,
											ran:Math.random()
										},
										dataType:'json',//服务器返回json格式数据
										type:'post',//HTTP请求类型
										timeout:10000,//超时时间设置为10秒；
										async:true,
										success:function(data){
											//服务器返回然后进行h5的本地存储
											try{
												websqlInsterDataToTable("wordtest",(ind),questionid,questionid,homeworkid,questionid,studentid,classid,"0");
											}catch(e){
												console.log("不支持websql");
											}
										},
										error:function(xhr,type,errorThrown){
											//异常处理；
											return errorThrown;
										}
									});
								}

								}catch(e){

								}

							});
						}
				}
				if(data.type==2||data.type==3){
					//设置jplayer属性
					tncontent=data[0].tncontent;
					wordreadid=data[0].id;
					//用户录音
					mui("div.wordtest").on("tap","p.micro",function(){

						if(isExitsFunction(UXinJSInterfaceSpeech.startRecordVoice)){
							try{
								var path=UXinJSInterfaceSpeech.startRecordVoice();
							}catch(e){
								mui.toast("对不起，有问题请联系客户服务部");
							}
						}else{
							mui.toast("请升级到最新的优教信使");
						}
					});

					//用户听自己的音频
					mui("div.wordtest").on("click","p.uservoice",function(){
						var mp3=this.getAttribute("mp3");
						if(mp3==''||mp3=='null'){
							mui.toast("请开始录音");
						}else{
							mp.play("http://192.168.151.126:8081/"+mp3);
						}
					});
				}
			}
      	    //进行播放事件的注入
      	    //播放按钮的播放

      	    if(data.type==1){
      	    	document.getElementById(muiindex).getElementsByClassName("lanren")[0].getElementsByClassName("audio-btn")[0].addEventListener('click',function(){
					var lanren=this.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("lanren")[0];
					var btn=this;
					var loading=this.parentNode.parentNode.parentNode.getElementsByClassName("loading")[0];
					var classname=this.getAttribute("class");
					var quesid=this.getAttribute("quesid");
					var type=this.getAttribute("type");
					var click=this.getElementsByClassName("sy-click")[0];
					if(classname.indexOf("playing")>0){
						stopaudio();
						this.setAttribute("class","");
					  	this.setAttribute("class","btn  pausing");
					  	this.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
					}
					else {
						console.log("bbbbbb");
						stopaudio();
						var obj=this;
						loading.style.display="";
						click.style.display="none";

					    loading.style.display="none";
						click.style.display="";
						//obj.getElementsByClassName("sy-click")[0].style.display="";
						listendata=[];
						//将数据载入数组中避免在同一个试题下音频的重复请求
						mui.each(page_tts[pageindex],function(k1,v1){
							var temp={};
							temp.tts_mp3=v1.tts_mp3;
							temp.tts_stoptime=v1.tts_stoptime;
							listendata.push(temp);
						});
						console.log(listendata);
						btn.setAttribute("class","");
						btn.setAttribute("class","btn playing");
						click.src="../../public/Homework/images/sy.gif";
						console.log(click);
						click.setAttribute("id","playing");
						mp.index = 0;
						mp.stemindex = 0;
						mp.queinitindex = 0;
						mp.questionindex = 0;
						mp.childstemindex = 0;
					    mp.childinitstemindex = 0;
					    mp.url = "";
					    mp.repeat = 1; //默认播放次数
					    mp.curpeat = 1;//当前播放到第几次
					    mp.url = "";

						question_play(parseInt(pagedata[pageindex].parent.question.questions_playtimes),listendata,click);
					 }
				});

      	    }else if(data.type==0){
      	    	//发声
      	    	if(data.parent.question.typeid==2){
      	    		mui("div.lanren").on('tap','a.audio-btn',function(){
						var mp3=this.getAttribute("mp3");
						var obj=this;
						//alert(mp3);
						this.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
						mp.play(word_mp3_url+mp3);
						$("#jplayer").bind($.jPlayer.event.ended,function(event){
			                obj.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						});
				   });

      	    	}

      	    }else if(data.type==2||data.type==3){
      	    	//设置jplayer属性
				tncontent=data[0].tncontent;
				wordreadid=data[0].id;
				//播放原音
				mui("div.wordtest").on("tap","p.micro",function(){
					var mp3=this.getAttribute("mp3");
					if(mp3.indexOf("mp3")>0){
						mp.play(word_mp3_url+mp3);
					}else{
						mp.play(text_mp3_url+mp3+".mp3");
					}
					
				});
				//用户听自己的音频
				mui("div.wordtest").on("click","p.uservoice",function(){
					var mp3=this.getAttribute("mp3");
					if(mp3==''||mp3=='null'){
						mui.toast("未作答");
					}else{
						mp.play("http://192.168.151.126:8081/"+mp3);
					}
				});
      	    }
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			mui.toast(errinfo);
			return errorThrown;
		}
});
}





//预览页面的数据
//请求响应
function getPreviewRespose(url,errinfo,index,source,id,pageindex,type){
	var respose="";
	listendata=[];
	mui.ajax(url,
		{
		data:{
			index:index,
			type:type,
			id:id,
			wtcount:wtcount,
			wacount:wacount,
			tacount:tacount,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:3000,//超时时间设置为3秒；
		async:false,
		success:function(data){
			pagedata[pageindex]=data;
			//服务器返回响应，根据响应结果，将试题进行赋值
			var obj={};
			if(data.children==null||data.children=='null'||data.children==''||data.children==undefined){
				obj.type=0;
			}else{
				obj.type=1;
			}
			if(data.type==1){
				obj.stemid=data.parent.question.stemid;
			}else{
				obj.stemid='0';
			}
			page_obj[pageindex]=obj;
			//类型的展示
			if(data.type==0){
				document.getElementById("type").innerHTML="单词测试";
			}else if(data.type==1){
				document.getElementById("type").innerHTML="听力训练";
			}else if(data.type==2){
				document.getElementById("type").innerHTML="单词跟读";
			}else if(data.type==3){
				document.getElementById("type").innerHTML="课文跟读";
			}
			if(data.type==0||data.type==1){
				content=setContent(data);
			}else if(data.type==2||data.type==3){
				content=aloundContent(data[0]);
			}
			var muiindex="item"+pageindex;
			document.getElementById(muiindex).innerHTML=content;
			page[pageindex]=1;
			if(data.type==1){
				page_tts[pageindex]=data.parent.tts;
			}
			//进行题干图片的压缩
			var objs=document.getElementsByClassName("tigan");
			mui.each(objs,function(){
				try{
					var imgs=this.getElementsByTagName("img");
					mui.each(imgs,function(key,value){
							var currimg=this;
							var image = new Image();
							image.src = this.src;
							var naturalWidth=0;
							image.onload = function(){
								var _stemp = this;
								naturalWidth=_stemp.width;
								console.log(screenwidth);
								if(naturalWidth>screenwidth){
										currimg.style.width=(screenwidth)+"px";
								}
							}

					});
				}catch(e){
				}
			});
      	    //播放按钮的播放
      	    if(data.type==1){
      	    	//document.getElementById(muiindex).getElementsByClassName("audio").addEventListener("tap",function(){});
      	    	document.getElementById(muiindex).getElementsByClassName("audio-btn")[0].addEventListener("click",function(){
					var lanren=this.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("lanren")[0];
					var btn=this;
					var loading=this.parentNode.parentNode.getElementsByClassName("loading")[0];
					var classname=this.getAttribute("class");
					var quesid=this.getAttribute("quesid");
					var type=this.getAttribute("type");
					var click=this.getElementsByClassName("sy-click")[0];
					if(classname.indexOf("playing")>0){
						stopaudio();
						this.setAttribute("class","");
					  	this.setAttribute("class","btn  pausing");
					  	this.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
					}
					else {
						var obj=this;
						var data=page_tts[pageindex];
						loading.style.display="";
						click.style.display="none";
						loading.style.display="none";
						click.style.display="";
						listendata=[];
						mui.each(data,function(k1,v1){
							var temp={};
							temp.tts_mp3=v1.tts_mp3;
							temp.tts_stoptime=v1.tts_stoptime;
							listendata.push(temp);
						});
						btn.setAttribute("class","");
						console.log(btn);
						btn.setAttribute("class","btn playing");
						click.src="../../public/Homework/images/sy.gif";
						click.setAttribute("id","playing");
						mp.index = 0;
						mp.stemindex = 0;
						mp.queinitindex = 0;
						mp.questionindex = 0;
						mp.childstemindex = 0;
					    mp.childinitstemindex = 0;
					    mp.url = "";
					    mp.repeat = 1; //默认播放次数
					    mp.curpeat = 1;//当前播放到第几次
					    mp.url = "";
							console.log(pageindex);
						question_play(parseInt(pagedata[pageindex].parent.question.questions_playtimes),listendata,click);
					 }
				});

      	    }else if(data.type==0){
      	    	//发声
      	    	if(data.parent.question.typeid==2){
      	    		mui("div.lanren").on('tap','a.audio-btn',function(){
						var mp3=this.getAttribute("mp3");
						var obj=this;
						this.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
						console.log(word_mp3_url+mp3);
						mp.play(word_mp3_url+mp3);
						$("#jplayer").bind($.jPlayer.event.ended,function(event){
			                obj.getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						});
				   });

      	    	}

      	     }else if(data.type==2){
      	    	//教师预览进行单词的原因发音
  	    		document.getElementsByClassName("uservoice")[pageindex].addEventListener("click",function(){
  	    			var mp3=this.getAttribute("mp3");
					mp.play(alound_mp3+mp3);
  	    		});

      	     }else if(data.type==3){
      	    	//教师预览进行课文的原因发音
  	    		document.getElementsByClassName("uservoice")[pageindex].addEventListener("click",function(){
  	    			var mp3=this.getAttribute("mp3");
					mp.play(alound_text+mp3+".mp3");
  	    		});
      	    }
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			mui.toast(errinfo);
			return errorThrown;
		}
});
}



//汇总页面的数据展示
function getWordtestSummaryData(id){
	var url="getSummary";
	var summaryhtml="";
	mui.ajax(url,
		{
		data:{
			studentId:studentid,
			classId:classid,
			homeworkid:homeworkid,
			examid:id,
			iserror:iserror,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			var wtcount=0;
			var eqcount=0;
			var wacount=0;
			var tacount=0;
			if(data.wa.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词朗读<p>";
				mui.each(data.wa,function(k,v){
					wacount=wacount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.score!=undefined&&v.score!=''&&v.score!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			var eqquesid="";
			if(data.wt.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词测试<p>";
				mui.each(data.wt,function(k,v){
					wtcount=wtcount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.score!=undefined&&v.score!=''&&v.score!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.isdo!=''&&v.isdo!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else if(v.isdo!=''&&v.isdo!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+k)+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			if(data.ta.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;课文朗读<p>";
				mui.each(data.ta,function(k,v){
					tacount=tacount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.isdo!=undefined&&v.isdo!=''&&v.isdo!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wtcount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wtcount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.score!=undefined&&v.score!=''&&v.score!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wtcount+k)+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="examsquiz?index='+(wacount+wtcount+k)+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			var eqquesid="";
;			if(data.eq.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;听力训练<p>";
				var kindex=0;
				mui.each(data.eq,function(k,v){
					if(v.id!=eqquesid){
						eqquesid=v.quesid;
						kindex=kindex+1;
					}
					i=(k+1);
					if(issubmit=='0'){
						if(v.score=='1'||v.score=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wtcount+wacount+tacount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wtcount+wacount+tacount+kindex-1)+'"><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}
					}else{
						if(v.isdo!=''&&v.isdo!='null'&&v.score=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wtcount+wacount+tacount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        }else if(v.isdo!=''&&v.isdo!='null'&&v.score=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wtcount+wacount+tacount+kindex-1)+'" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="examsquiz?index='+(wtcount+wacount+tacount+kindex-1)+'" style=""><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}
					}
				});
			}
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errorThrown;
		}
	});
	return summaryhtml;
}



//汇总页面的数据展示
function getPreviewSummaryData(wt,eq){
	//var url="getWordtestSummaryData";
	var url="getPreviewSummary";
	var summaryhtml="";
	mui.ajax(url,
		{
		data:{
			wa:wa,
			wt:wt,
			ta:ta,
			eq:eq,
			ran:Math.random()
		},
		dataType:'json',//服务器返回json格式数据
		type:'post',//HTTP请求类型
		timeout:10000,//超时时间设置为10秒；
		async:false,
		success:function(data){
			var wtcount=0;
			var wacount=0;
			var tacount=0;
			var eqcount=0;
			if(data.wordalound.length>0){

				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词跟读<p>";
				mui.each(data.wordalound,function(k,v){
					wacount=wacount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.answer!=undefined&&v.answer!=''&&v.answer!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			if(data.wordtest.length>0){

				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;单词测试<p>";
				mui.each(data.wordtest,function(k,v){
					wtcount=wtcount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.answer!=undefined&&v.answer!=''&&v.answer!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			if(data.textalound.length>0){

				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;课文跟读<p>";
				mui.each(data.textalound,function(k,v){
					tacount=tacount+1;
					i=(k+1);
					if(issubmit=='0'){
						if(v.answer!=undefined&&v.answer!=''&&v.answer!='null'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'" class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary"><span class="mui-icon">'+i+'</span></a>';
			        	}
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" loc="preview?index='+k+'"  class="quessummary" style=""><span class="mui-icon">'+i+'</span></a>';
			        	}
					}
				});
			}
			var eqquesid="";
			if(data.examsquiz.length>0){
				//服务器返回然后进行h5的本地存储
				summaryhtml=summaryhtml+"<p style='margin-top:20px;'>&nbsp;&nbsp;听力训练<p>";
				var kindex=0;
				mui.each(data.examsquiz,function(k,v){
					if(v.quesid!=eqquesid){
						eqquesid=v.quesid;
						kindex=kindex+1;
					}
					i=(k+1);
					if(issubmit=='0'){
						if(v.iscorrect=='1'||v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="preview?index='+(wtcount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="preview?index='+(wtcount+kindex-1)+'"><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}
					}else{
						if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='1'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="preview?index='+(wtcount+kindex-1)+'" style="background: rgb(43, 200, 160);"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        }else if(v.answer!=''&&v.answer!='null'&&v.iscorrect=='0'){
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="preview?index='+(wtcount+kindex-1)+'" style="background-color:darkgoldenrod;"><span class="mui-icon" style="color:white;">'+i+'</span></a>';
			        	}else{
			        		summaryhtml=summaryhtml+'<a id="icon-icon-contact" class="quessummary" loc="preview?index='+(wtcount+kindex-1)+'" style=""><span class="mui-icon" style="colar:black">'+i+'</span></a>';
			        	}
					}
				});
			}
		},
		error:function(xhr,type,errorThrown){
			//异常处理；
			return errorThrown;
		}
	});
	return summaryhtml;
}




//获取mp3文件路径
function getmp3url(mp3name){
	//mp3name = mp3name.substr(0,mp3name.length-1);
	var mp3url = '';
	var quespeed = 1;
	//if(examstts_type == 1){			//系统生成
		if(quespeed == 0){
			mp3name = mp3name+'s';
		}
		else if(quespeed == 2){
			mp3name = mp3name+'q';
		}
	//}
	mp3url = exams_mp3_url+mp3name.substr(0,2)+'/'+mp3name+'.mp3';
	return mp3url;
}




//小题播放
function question_play(playtimes,quettsdata,obj){

	var smallquetts = '';
	console.log(playtimes);
	clearTimeout();
	console.log(mp.playtimes);
	//播放次数

	if(mp.playtimes<playtimes){
		console.log(mp.playtimes);
		if(mp.questionindex < quettsdata.length){

			smallquetts = quettsdata[mp.questionindex];

			playurl = getmp3url(smallquetts.tts_mp3);
			console.log(playurl);
			mp.play(playurl);
			$("#jplayer").bind($.jPlayer.event.ended,function(event){
				clearTimeout(mp3_progress);
				mp3_progress = setTimeout(function(){
					mp.questionindex = mp.questionindex +1;
					console.log(mp.questionindex);
					question_play(playtimes,quettsdata,obj);
				},parseInt(quettsdata[mp.questionindex].tts_stoptime)*1000);
			});
		}else{
			 mp.index = 0;
			 mp.stemindex = 0;
			 mp.queinitindex = 0;
			 mp.questionindex = 0;
			 mp.childstemindex = 0;
		    mp.childinitstemindex = 0;
		    mp.url = "";
		    mp.repeat = 1; //默认播放次数
		    mp.curpeat = 1;//当前播放到第几次
		    mp.url = "";
            console.log(obj);
		    obj.parentNode.setAttribute("class","");
		  	obj.parentNode.setAttribute("class","audio-btn btn");
		  	try{
		  		var playing=document.getElementById("playing");
			    playing.setAttribute("src","../../public/Homework/images/sy.png");
			  	//stopaudio();
		  	}catch(e){
		  		//stopaudio();
		  		console.log(e);
		  	}
				mp.playtimes=mp.playtimes+1;
				clearTimeout(mp3_progress_reap);
				mp3_progress_reap=setTimeout(function(){
				    obj.parentNode.setAttribute("class","");
					obj.parentNode.setAttribute("class","btn playing");
					var playings=document.getElementById("playing");
			        playings.setAttribute("src","../../public/Homework/images/sy.gif");
                    question_play(playtimes,quettsdata,obj);
				},5000);
		    //document.getElementById("audio-btn").style.backgroundImage="url(../../public/Homework/images/pause-to-play-faster.gif)";
		}

	}else{
		mp.index = 0;
		mp.stemindex = 0;
		mp.queinitindex = 0;
		mp.questionindex = 0;
		mp.childstemindex = 0;
		 mp.childinitstemindex = 0;
		 mp.url = "";
		 mp.repeat = 1; //默认播放次数
		 mp.curpeat = 1;//当前播放到第几次
		 mp.url = "";
     mp.playtimes=0;
		 obj.parentNode.setAttribute("class","");
		 obj.parentNode.setAttribute("class","audio-btn btn");
		 try{
			 var playing=document.getElementById("playing");
			 playing.setAttribute("src","../../public/Homework/images/sy.png")
			 playing.removeAttribute("id");
			 //stopaudio();
		 }catch(e){
			 //stopaudio();
			 console.log(e);
		 }
	}

}

console.log(pageslider);
function hwstopaudio(i){
	console.log("kaishi"+i);
	console.log("stop audio");
	var perpage=0;
	console.log(pageslider);
	try{
		if(i==0||pageslider[i-1]==undefined){
			perpage=0;
		}else{
			perpage=pageslider[i-1];
		}
	}catch(e){
       console.log("error");
	}
	var nxtpage=0;
	try{
		if(pageslider[i+1]==undefined){
			nxtpage=0;
		}else{
			nxtpage=pageslider[i+1];
		}
	}catch(e){

	}
	console.log(pageslider);
	pageslider[i]=perpage>nxtpage?perpage+1:nxtpage+1;
	console.log(pageslider);
	console.log(page_obj);
	if(page_obj[i].type!=1){
		try{
			document.getElementById("playing").setAttribute("src","../../public/Homework/images/sy.png");
			removeClass(document.getElementById("playing").parentNode,"playing");
		}catch(e){

		}
		stopaudio();
		try{
			document.getElementById("playing").removeAttribute("id");
		}catch(e){

		}
		//return 1;
	}else{
		if(page_obj[i].type==0){
			stopaudio();
			//return 1;
		}else if(page_obj[i].type==1){
			console.log("ccccccccc");
			//组合试题从哪里过来的
			try{
				document.getElementById("playing").setAttribute("src","../../public/Homework/images/sy.png");

			}catch(e){
                console.log("meishaosao");
			}
			if(pageslider[i]==(pageslider[i-1]+1)){
				//表示从左边过来的数据判断左边的情况需要将昨天的那个直接停止了
				if(page_obj[i].stemid!=page_obj[i-1].stemid){
					try{
						removeClass(document.getElementById("playing").parentNode,"playing");
					}catch(e){

					}

					console.log("left left left left");
					try{
						document.getElementById("playing").removeAttribute("id");
						//设置当前的是正在播放的情况
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
					}catch(e){
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
					}
					stopaudio();
					//return 1;
				}else{
					console.log("left left left leftkaishi");
					try{
						//设置当前的是正在播放的情况
						console.log(document.getElementById("playing"));
						if(hasClass(document.getElementById("playing").parentNode,"playing")){
							try{
								console.log("aaaaaa");
								removeClass(document.getElementById("playing").parentNode,"playing");
							}catch(e){
                                
							}
							document.getElementById("playing").removeAttribute("id");
							document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
						    document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
						    addClass(document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].parentNode,"playing");
						}
					}catch(e){
                         console.log("aaaerror");
					}
					//return 0;

				}

			}else if(pageslider[i]==(pageslider[i+1]+1)){
				//表示从左边过来的数据判断右边的情况
				if(page_obj[i].stemid!=page_obj[i+1].stemid){
					try{
						removeClass(document.getElementById("playing").parentNode,"playing");
					}catch(e){

					}
					try{
						document.getElementById("playing").removeAttribute("id");
						//设置当前的是正在播放的情况
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
					}catch(e){
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.png";
						document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");

					}
					stopaudio();
					//return 1;
				}else{
					try{
						//设置当前的是正在播放的情况判断目前的上一个是不是在播放状态
						if(hasClass(document.getElementById("playing").parentNode,"playing")){
							try{
								removeClass(document.getElementById("playing").parentNode,"playing");
							}catch(e){

							}
							document.getElementById("playing").removeAttribute("id");
							console.log("vvvvvvvvvv");
							document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].src="../../public/Homework/images/sy.gif";
						    document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].setAttribute("id","playing");
						    addClass(document.getElementsByClassName("ques")[i].getElementsByClassName("sy-click")[0].parentNode,"playing");
						}
					}catch(e){

					}
					//return 0;
				}
			}
		}
	}
}

//停止MP3播放
function stopaudio(){
	try{
		clearTimeout(mp3_progress);
		clearTimeout(mp3_progress_reap);
	}catch(e){
		console.log(e);
	}
	try{
		mp.pause();
	}catch(e){
		console.log(e);
	}
    mp3_progress='';
    mp3_progress_reap='';
	mp.index = 0;
	mp.stemindex = 0;
	mp.queinitindex = 0;
	mp.questionindex = 0;
	mp.childstemindex = 0;
	mp.playtimes=0;
    mp.childinitstemindex = 0;
    mp.url = "";
    mp.repeat = 1; //默认播放次数
    mp.curpeat = 1;//当前播放到第几次
    mp.url = "";
}

//js实现的辅助函数
function addClass(obj, cls){
    var obj_class = obj.className;//获取 class 内容.
    var blank = (obj_class != '') ? ' ' : '';//判断获取到的 class 是否为空, 如果不为空在前面加个'空格'.
    var added = obj_class + blank + cls;//组合原来的 class 和需要添加的 class.
    obj.className = added;//替换原来的 class.
}

function removeClass(obj, cls){
    var obj_class = ' '+obj.className+' ';//获取 class 内容, 并在首尾各加一个空格. ex) 'abc        bcd' -> ' abc        bcd '
    obj_class = obj_class.replace(/(\s+)/gi, ' ');//将多余的空字符替换成一个空格. ex) ' abc        bcd ' -> ' abc bcd '
    var removed = obj_class.replace(' '+cls+' ', ' ');//在原来的 class 替换掉首尾加了空格的 class. ex) ' abc bcd ' -> 'bcd '
    removed = removed.replace(/(^\s+)|(\s+$)/g, '');//去掉首尾空格. ex) 'bcd ' -> 'bcd'
    obj.className = removed;//替换原来的 class.
}

function hasClass(obj, cls){
    var obj_class = obj.className;//获取 class 内容.
    var obj_class_lst = obj_class.split(/\s+/);//通过split空字符将cls转换成数组.
    var x = 0;
    for(x in obj_class_lst) {
        if(obj_class_lst[x] == cls) {//循环数组, 判断是否包含cls
            return true;
        }
    }
    return false;
}

//口语的样式
function aloundContent(data){
	var content="";
	content=content+'<div class="title tigan" style="margin-top:20%;font-size:1.2emem;font-family: times;color:black;text-align:center;margin-left:10px;margin-right:10px;padding-left:10px;padding-right:10px;">';
	content=content+data.tncontent;
	content=content+'</div><h5 style="margin-top:10px;font-family: times;text-align:center;margin-left:10px;margin-right:10px;padding-left:10px;padding-right:10px;">'+data.cncontent+'</h5>';
	content=content+'<div class="fen3 edi-dc-left03b wordtest" style="margin-top:20px;margin-left:30px;margin-right:10px;padding-left:10px;padding-right:10px;">';
	//if(issubmit==0){
	content=content+'<p class="uservoice" mp3="'+data.usermp3+'"><a class="btn-bo02 bo01 edi-yuan"><i class="fa fa-volume-up fa-18"></i></a></p>';
//	}else{
//		content=content+'<p class="uservoice" mp3="'+data.wordmp3+'"><a class="btn-bo02 bo01 edi-yuan"><i class="fa fa-volume-up fa-18"></i></a></p>';
//	}
	if(issubmit==0){
		content=content+'<p class="micro quesvoice" mp3="'+data.usermp3+'"><a class="btn-bo02 bo01 edi-yuan"><i class="fa fa-microphone fa-18"></i></a></p>';
	}else{
		content=content+'<p class="micro quesvoice" mp3="'+data.wordmp3+'"><a class="btn-bo02 bo01 edi-yuan"><i class="fa fa-microphone fa-18"></i></a></p>';
	}

	content=content+'<p><a class="rig-org">';
	//判断出现几颗星星
	var startint=parseInt(data.userscore);
	if(startint>0&&startint<40){
		content=content+'<i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
	}else if(startint>=40&&startint<75){
		content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star edi-gg"></i>';
	}else if(startint>=75&&startint<=100){
		content=content+'<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>';
	}else{
		content=content+'<i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i><i class="fa fa-star edi-gg"></i>';
	}
	content=content+'<br><strong class="score">';
	if(data.userscore==null){
		content=content+'0</strong>分</a></p>';
	}else{
		content=content+data.userscore+'</strong>分</a></p>';
	}

	content=content+'</div>';
	if(issubmit==1){
		content=content+'<div class="mui-content-padded" style="font-size:100%;color: #8f8f94;">';
		content=content+'<h5 style="margin-bottom:10px;">班级数据</h5>';
		content=content+'<ul class="mui-table-view mui-grid-view" style="background-color: white;">';
		content=content+'<li class="mui-table-view-cell" style="width: 32%;">';
		content=content+'<a href="javascript:void(0);">';
		content=content+'<span style="font-size:100%;">';
		content=content+'作答人数';
		content=content+'</span>';
		if(data.num==undefined){
			data.num=0
		}
		content=content+'<div class="mui-media-body" style="font-size:100%;color: #8f8f94;">'+data.num+'</div>';
		content=content+'</a>';
		content=content+'</li>';
		content=content+'<li class="mui-table-view-cell" style="width: 32%;">';
		content=content+'<a href="javascript:void(0);">';
		content=content+'<span style="font-size:100%;">';
		content=content+'及格人数';
		content=content+'</span>';
		if(data.accnumn==undefined){
			data.accnum=0
		}
		content=content+'<div class="mui-media-body" style="font-size:100%;color: #8f8f94;">'+data.accnum+'</div>';
		content=content+'</a>';
		content=content+'</li>';
		content=content+'<li class="mui-table-view-cell" style="width: 32%;">';
		content=content+'<a href="javascript:void(0);">';
		content=content+'<span style="font-size:100%;">';
		content=content+'最高分';
		content=content+'</span>';
		if(data.maxscore=='null'||data.maxscore==undefined||data.maxscore==""){
			content=content+'<div class="mui-media-body" style="font-size:100%;color: #8f8f94;">0</div>';
		}else{
			content=content+'<div class="mui-media-body" style="font-size:100%;color: #8f8f94;">'+data.maxscore+'</div>';
		}
		content=content+'</a>';
		content=content+'</li>';
		content=content+'</ul>';
		content=content+'</div>';
	}
	return content;
}

//判断手机的类型
/**
 * [isMobile 判断平台]
 * @param test: 0:iPhone    1:Android
 */
function ismobile(test){
    var u = navigator.userAgent, app = navigator.appVersion;
    if(/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))){
     if(window.location.href.indexOf("?mobile")<0){
      try{
       if(/iPhone|mac|iPod|iPad/i.test(navigator.userAgent)){
        return '0';
       }else{
        return '1';
       }
      }catch(e){}
     }
    }else if( u.indexOf('iPad') > -1){
        return '0';
    }else{
        return '1';
    }
};
