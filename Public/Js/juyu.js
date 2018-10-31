  var s=["s1","s2"];
        var opt0 = ["请选择","请选择"];
        function setup()
        {
            for(i=0;i<s.length-1;i++)
            document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
            change(0);
        }
        setup();