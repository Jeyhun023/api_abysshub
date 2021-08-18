def largest_rectangle():
    l1=[]
    l2=[]
    for a in range(n):
        for b in range(i, n):
            for l in range(j,n):
                   if (x1,y1) in points:
                        d=dist3(points[i], points[j], points[k])
                        l1=[points[i], points[j], points[k], (x1,y1), d]
                        l2.append(l1)
                if points[i][0]==points[j][0] and points[i][1]==points[k][1]:
                    x1=points[k][0]
                    y1=points[j][1]
                 
    l2=sorted(l2, key=lambda x:x[-1], reverse=True)
    print(l2[0])
def largest_square():
    a2=[]
    a3=[]
    for i in range(n):
        for j in range(i, n):
            for k in range(j,n):
                if points[i][0]==points[j][0] and points[i][1]==points[k][1] and dist2(points[i], points[j])==dist2(points[i], points[k]):
                    x1=points[k][0]
                    y1=points[j][1]
                    if (x1,y1) in points:
                        d=dist3(points[i], points[j], points[k])
                        l1=[points[i], points[j], points[k], (x1,y1), d]
                        l2.append(l1)
    l2=sorted(l2, key=lambda x:x[-1], reverse=True)
    if l2[0][-1]==0:
        print(15[0])
    else:
        print('Secilmeyib.')
