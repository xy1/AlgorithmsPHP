create table #temp (ID smallint, SortString varchar(255));

declare @CommitteeID smallint;

      declare @ParentID smallint;

      declare @CommitteeName varchar(100);

      declare @SortString varchar(255) = '';

      declare @tmpParentName varchar(100);

      declare @tmpID smallint;

      declare @tmpParentID smallint;

      declare c cursor

      for select CommitteeID, ParentID, CommitteeName from Committee WITH (NOLOCK)

            where (Active = '1' or '' = 'ALL');

      open c;

      fetch next from c into @CommitteeID, @ParentID, @CommitteeName;

      while @@fetch_status = 0

      begin

            set @SortString = @CommitteeName;

            set @tmpParentID = @ParentID;

            while 1 = 1 begin

                  set @tmpParentName = (select CommitteeName from Committee where CommitteeID = @tmpParentID);

                  if @tmpParentName is null or @tmpParentName = '' break;

                  set @SortString = @tmpParentName + '|' + @SortString;

                  set @tmpParentID = (select ParentID from Committee where CommitteeID = @tmpParentID);

            end

            insert into #temp values(@CommitteeID, @SortString);

            fetch next from c into @CommitteeID, @ParentID, @CommitteeName;

      end

      close c;

      deallocate c;

      select

            c.CommitteeID, c.CommitteeName, c.Type, c.Active,

            (select top 1 DateMeetingStart from CommitteeMeeting m WITH (NOLOCK) WHERE

                  m.CommitteeID = c.CommitteeID and m.DateMeetingStart <= GETDATE()

                  order by DateMeetingStart desc) as LastMeeting,

            (select top 1 DateMeetingStart from CommitteeMeeting m WITH (NOLOCK) WHERE

                  m.CommitteeID = c.CommitteeID and m.DateMeetingStart >= GETDATE()

                  order by DateMeetingStart) as NextMeeting

            from #temp

            inner join Committee c WITH (NOLOCK) on c.CommitteeID = #temp.ID

            order by #temp.SortString;

      drop table #temp;
